<?php
/**
 * Event Recurrence Engine
 *
 * Implements schedule calculations, limits, and summary generation.
 *
 * @package Weardale_Platform
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Calculates theoretical occurrence slots based on event recurrence settings.
 */
function weardale_platform_calculate_recurrences( $event_id ) {
    $slots = array();
    
    $start_date = get_post_meta( $event_id, '_event_date', true );
    $end_date   = get_post_meta( $event_id, '_event_end_date', true );
    $start_time = get_post_meta( $event_id, '_event_start_time', true );
    $end_time   = get_post_meta( $event_id, '_event_end_time', true );
    
    if ( empty( $start_date ) ) {
        return $slots;
    }
    
    if ( empty( $end_date ) ) {
        $end_date = $start_date;
    }
    if ( empty( $start_time ) ) {
        $start_time = '00:00:00';
    }
    if ( empty( $end_time ) ) {
        $end_time = '23:59:59';
    }
    
    // Retrieve recurrence options
    $mode         = get_post_meta( $event_id, '_event_recurrence_mode', true );
    $interval     = intval( get_post_meta( $event_id, '_event_recurrence_interval', true ) );
    $end_type     = get_post_meta( $event_id, '_event_recurrence_end_type', true );
    $end_date_lim = get_post_meta( $event_id, '_event_recurrence_end_date', true );
    $end_cnt_lim  = intval( get_post_meta( $event_id, '_event_recurrence_end_count', true ) );
    
    if ( $interval <= 0 ) {
        $interval = 1;
    }
    if ( empty( $end_type ) ) {
        $end_type = 'count';
    }
    if ( $end_cnt_lim <= 0 ) {
        $end_cnt_lim = 250;
    }
    
    $max_occurrences = min( 250, $end_cnt_lim );
    
    try {
        $timezone = wp_timezone();
        $start_dt = new DateTime( $start_date . ' ' . $start_time, $timezone );
        $first_end_dt = new DateTime( $end_date . ' ' . $end_time, $timezone );
        
        // Duration of a single occurrence in seconds
        $duration = $first_end_dt->getTimestamp() - $start_dt->getTimestamp();
        if ( $duration < 0 ) {
            $duration = 0;
        }
        
        switch ( $mode ) {
            case 'daily':
                $current_dt = clone $start_dt;
                $count = 0;
                while ( $count < $max_occurrences ) {
                    if ( $end_type === 'date' && ! empty( $end_date_lim ) ) {
                        $limit_dt = new DateTime( $end_date_lim . ' 23:59:59', $timezone );
                        if ( $current_dt > $limit_dt ) {
                            break;
                        }
                    }
                    
                    $slots[] = array(
                        'start' => $current_dt->format('Y-m-d H:i:s'),
                        'end'   => date('Y-m-d H:i:s', $current_dt->getTimestamp() + $duration),
                    );
                    
                    $count++;
                    $current_dt->modify( "+{$interval} days" );
                }
                break;
                
            case 'weekly':
                $weekdays_meta = get_post_meta( $event_id, '_event_recurrence_weekdays', true );
                if ( ! is_array( $weekdays_meta ) || empty( $weekdays_meta ) ) {
                    $weekdays = array( strtolower( $start_dt->format('l') ) );
                } else {
                    $weekdays = array_map( 'strtolower', $weekdays_meta );
                }
                
                $start_of_week = clone $start_dt;
                $start_of_week->modify('monday this week');
                
                $count = 0;
                $week_offset = 0;
                while ( $count < $max_occurrences ) {
                    $week_dt = clone $start_of_week;
                    if ( $week_offset > 0 ) {
                        $week_dt->modify( "+{$week_offset} weeks" );
                    }
                    
                    $week_slots = array();
                    foreach ( $weekdays as $day_name ) {
                        $day_dt = clone $week_dt;
                        if ( $day_name !== 'monday' ) {
                            $day_dt->modify( $day_name );
                        }
                        
                        // Check if day_dt falls on or after start_date
                        if ( $day_dt->format('Y-m-d') >= $start_dt->format('Y-m-d') ) {
                            if ( $end_type === 'date' && ! empty( $end_date_lim ) ) {
                                $limit_dt = new DateTime( $end_date_lim . ' 23:59:59', $timezone );
                                if ( $day_dt > $limit_dt ) {
                                    continue;
                                }
                            }
                            
                            $time_parts = explode( ':', $start_time );
                            $day_dt->setTime( intval($time_parts[0]), intval($time_parts[1]), isset($time_parts[2]) ? intval($time_parts[2]) : 0 );
                            
                            $week_slots[] = array(
                                'timestamp' => $day_dt->getTimestamp(),
                                'start'     => $day_dt->format('Y-m-d H:i:s'),
                                'end'       => date('Y-m-d H:i:s', $day_dt->getTimestamp() + $duration),
                            );
                        }
                    }
                    
                    // Sort weekly occurrences chronologically
                    usort( $week_slots, function( $a, $b ) {
                        return $a['timestamp'] - $b['timestamp'];
                    } );
                    
                    foreach ( $week_slots as $slot ) {
                        if ( $count >= $max_occurrences ) {
                            break;
                        }
                        $slots[] = $slot;
                        $count++;
                    }
                    
                    $week_offset += $interval;
                    if ( $week_offset > 1000 ) {
                        break; // fail-safe
                    }
                }
                break;
                
            case 'monthly':
                $monthly_type = get_post_meta( $event_id, '_event_recurrence_monthly_type', true ); // day_of_month, relative_weekday
                if ( empty( $monthly_type ) ) {
                    $monthly_type = 'day_of_month';
                }
                
                $count = 0;
                $month_offset = 0;
                
                if ( 'day_of_month' === $monthly_type ) {
                    $day_of_month = intval( $start_dt->format('j') );
                    while ( $count < $max_occurrences ) {
                        $target_dt = clone $start_dt;
                        if ( $month_offset > 0 ) {
                            $target_dt->modify( "+{$month_offset} months" );
                        }
                        
                        $max_days = intval( $target_dt->format('t') );
                        $actual_day = min( $day_of_month, $max_days );
                        $target_dt->setDate( intval($target_dt->format('Y')), intval($target_dt->format('n')), $actual_day );
                        
                        if ( $end_type === 'date' && ! empty( $end_date_lim ) ) {
                            $limit_dt = new DateTime( $end_date_lim . ' 23:59:59', $timezone );
                            if ( $target_dt > $limit_dt ) {
                                break;
                            }
                        }
                        
                        $slots[] = array(
                            'start' => $target_dt->format('Y-m-d H:i:s'),
                            'end'   => date('Y-m-d H:i:s', $target_dt->getTimestamp() + $duration),
                        );
                        
                        $count++;
                        $month_offset += $interval;
                    }
                } else {
                    // relative_weekday (e.g. Second Tuesday of month)
                    $weekday_name = $start_dt->format('l');
                    $day_num      = intval( $start_dt->format('j') );
                    $week_num     = ceil( $day_num / 7 );
                    
                    $week_words = array( 1 => 'first', 2 => 'second', 3 => 'third', 4 => 'fourth', 5 => 'fifth' );
                    $week_word  = isset( $week_words[ $week_num ] ) ? $week_words[ $week_num ] : 'last';
                    
                    while ( $count < $max_occurrences ) {
                        $target_dt = clone $start_dt;
                        if ( $month_offset > 0 ) {
                            $target_dt->modify( "+{$month_offset} months" );
                        }
                        
                        $month_year = $target_dt->format('F Y');
                        $relative_str = "{$week_word} {$weekday_name} of {$month_year}";
                        
                        try {
                            $day_dt = new DateTime( $relative_str, $timezone );
                        } catch ( Exception $e ) {
                            // fallback to end of month if relative calculation fails
                            $day_dt = clone $target_dt;
                            $day_dt->modify('last day of this month');
                        }
                        
                        $time_parts = explode( ':', $start_time );
                        $day_dt->setTime( intval($time_parts[0]), intval($time_parts[1]), isset($time_parts[2]) ? intval($time_parts[2]) : 0 );
                        
                        if ( $end_type === 'date' && ! empty( $end_date_lim ) ) {
                            $limit_dt = new DateTime( $end_date_lim . ' 23:59:59', $timezone );
                            if ( $day_dt > $limit_dt ) {
                                break;
                            }
                        }
                        
                        // Ensure relative day is not before initial start date
                        if ( $day_dt->format('Y-m-d') >= $start_dt->format('Y-m-d') ) {
                            $slots[] = array(
                                'start' => $day_dt->format('Y-m-d H:i:s'),
                                'end'   => date('Y-m-d H:i:s', $day_dt->getTimestamp() + $duration),
                            );
                            $count++;
                        }
                        
                        $month_offset += $interval;
                    }
                }
                break;
        }
        
    } catch ( Exception $e ) {
        // Return single slot as safe fallback if parsing fails
        $slots[] = array(
            'start' => $start_date . ' ' . $start_time,
            'end'   => $end_date . ' ' . $end_time,
        );
    }
    
    return $slots;
}

/**
 * Generates a human-friendly description of the recurrence schedule.
 */
function weardale_platform_generate_recurrence_summary( $event_id ) {
    $is_recurring = get_post_meta( $event_id, '_event_is_recurring', true ) === '1';
    if ( ! $is_recurring ) {
        return __( 'One-off activity', 'weardale-platform' );
    }
    
    $mode         = get_post_meta( $event_id, '_event_recurrence_mode', true );
    $interval     = intval( get_post_meta( $event_id, '_event_recurrence_interval', true ) );
    $end_type     = get_post_meta( $event_id, '_event_recurrence_end_type', true );
    $end_date_lim = get_post_meta( $event_id, '_event_recurrence_end_date', true );
    $end_cnt_lim  = intval( get_post_meta( $event_id, '_event_recurrence_end_count', true ) );
    
    if ( $interval <= 0 ) {
        $interval = 1;
    }
    
    $summary = '';
    
    switch ( $mode ) {
        case 'daily':
            if ( 1 === $interval ) {
                $summary = __( 'Every day', 'weardale-platform' );
            } else {
                /* translators: %d: interval in days */
                $summary = sprintf( __( 'Every %d days', 'weardale-platform' ), $interval );
            }
            break;
            
        case 'weekly':
            $weekdays = get_post_meta( $event_id, '_event_recurrence_weekdays', true );
            $days_str = '';
            if ( is_array( $weekdays ) && ! empty( $weekdays ) ) {
                $days_formatted = array_map( 'ucfirst', $weekdays );
                $days_str = implode( ', ', $days_formatted );
            } else {
                $start_date = get_post_meta( $event_id, '_event_date', true );
                if ( ! empty( $start_date ) ) {
                    $days_str = date( 'l', strtotime( $start_date ) );
                }
            }
            
            if ( 1 === $interval ) {
                /* translators: %s: selected weekdays list */
                $summary = sprintf( __( 'Every week on %s', 'weardale-platform' ), $days_str );
            } else {
                /* translators: 1: interval in weeks, 2: selected weekdays list */
                $summary = sprintf( __( 'Every %1$d weeks on %2$s', 'weardale-platform' ), $interval, $days_str );
            }
            break;
            
        case 'monthly':
            $monthly_type = get_post_meta( $event_id, '_event_recurrence_monthly_type', true );
            if ( 'relative_weekday' === $monthly_type ) {
                $start_date = get_post_meta( $event_id, '_event_date', true );
                if ( ! empty( $start_date ) ) {
                    $day_num = intval( date( 'j', strtotime( $start_date ) ) );
                    $weekday = date( 'l', strtotime( $start_date ) );
                    $week_num = ceil( $day_num / 7 );
                    $words = array( 1 => 'first', 2 => 'second', 3 => 'third', 4 => 'fourth', 5 => 'last' );
                    $word = isset( $words[ $week_num ] ) ? $words[ $week_num ] : 'last';
                    
                    if ( 1 === $interval ) {
                        /* translators: 1: ordinal week word, 2: weekday name */
                        $summary = sprintf( __( 'Monthly on the %1$s %2$s', 'weardale-platform' ), $word, $weekday );
                    } else {
                        /* translators: 1: interval in months, 2: ordinal week word, 3: weekday name */
                        $summary = sprintf( __( 'Every %1$d months on the %2$s %3$s', 'weardale-platform' ), $interval, $word, $weekday );
                    }
                } else {
                    $summary = __( 'Monthly on relative day', 'weardale-platform' );
                }
            } else {
                $start_date = get_post_meta( $event_id, '_event_date', true );
                $day_of_month = ! empty( $start_date ) ? date( 'jS', strtotime( $start_date ) ) : 'same';
                if ( 1 === $interval ) {
                    /* translators: %s: day of month (e.g. 14th) */
                    $summary = sprintf( __( 'Monthly on the %s day', 'weardale-platform' ), $day_of_month );
                } else {
                    /* translators: 1: interval in months, 2: day of month (e.g. 14th) */
                    $summary = sprintf( __( 'Every %1$d months on the %2$s day', 'weardale-platform' ), $interval, $day_of_month );
                }
            }
            break;
    }
    
    // Add end constraint summary
    if ( 'date' === $end_type && ! empty( $end_date_lim ) ) {
        $formatted_end = date( 'j F Y', strtotime( $end_date_lim ) );
        /* translators: 1: existing recurrence statement, 2: end date */
        $summary = sprintf( __( '%1$s until %2$s', 'weardale-platform' ), $summary, $formatted_end );
    } elseif ( 'count' === $end_type && $end_cnt_lim > 0 ) {
        /* translators: 1: existing recurrence statement, 2: occurrence count */
        $summary = sprintf( _n( '%1$s, %2$d time', '%1$s, %2$d times', $end_cnt_lim, 'weardale-platform' ), $summary, $end_cnt_lim );
    }
    
    return $summary;
}
