export interface WTEvent {
  id: string;
  title: string;
  content: string;
  date: string;
  time: string;
  location: string;
  cost: string;
  strand: 'cafe' | 'creative' | 'youth' | 'roots-shoots';
}

export interface WTPost {
  id: string;
  title: string;
  excerpt: string;
  content: string;
  date: string;
  author: string;
  strand?: 'cafe' | 'creative' | 'youth' | 'roots-shoots';
}

export interface WPFile {
  name: string;
  path: string;
  type: 'theme' | 'plugin' | 'doc' | 'script';
  content: string;
}
