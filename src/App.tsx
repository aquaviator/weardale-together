import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { 
  Coffee, 
  Palette, 
  Trees, 
  Baby, 
  Calendar, 
  FileText, 
  Info, 
  Mail, 
  MapPin, 
  Phone, 
  ChevronRight, 
  ChevronDown,
  Menu,
  X,
  Clock, 
  DollarSign, 
  Check, 
  Code, 
  Database, 
  FileCode, 
  HelpCircle,
  PlusCircle,
  Send,
  User,
  Heart
} from 'lucide-react';
import { starterEvents, starterPosts, wordpressFiles } from './data';
import { WTEvent, WTPost, WPFile } from './types';

export default function App() {
  const [activeTab, setActiveTab] = useState<string>('home');
  const [events, setEvents] = useState<WTEvent[]>(starterEvents);
  const [posts, setPosts] = useState<WTPost[]>(starterPosts);

  // Navigation states
  const [programmesOpen, setProgrammesOpen] = useState(false);
  const [involvedOpen, setInvolvedOpen] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [mobileProgrammesOpen, setMobileProgrammesOpen] = useState(false);
  const [mobileInvolvedOpen, setMobileInvolvedOpen] = useState(false);

  // Modals for About and Directory
  const [showAboutModal, setShowAboutModal] = useState(false);
  const [showDirectoryModal, setShowDirectoryModal] = useState(false);
  
  // Simulation Story Editorial Metadata States
  const [selectedSimPostId, setSelectedSimPostId] = useState<string>('p4'); // Default to 'A SONG for Weardale'
  const [metaForm, setMetaForm] = useState({
    featured: false,
    strand: 'creative',
    relatedEvent: "Andy's Event",
    relatedDirectory: "Weardale Creative Makers Guild"
  });
  const [saveSuccess, setSaveSuccess] = useState(false);
  const [consoleTraceLog, setConsoleTraceLog] = useState<string>('');

  const currentSimPost = posts.find(p => p.id === selectedSimPostId);

  const handleSaveMetaForm = () => {
    const updated = posts.map(p => {
      if (p.id === selectedSimPostId) {
        return {
          ...p,
          featured: metaForm.featured,
          strand: metaForm.strand as any,
          relatedEvent: metaForm.relatedEvent,
          relatedDirectory: metaForm.relatedDirectory
        };
      } else {
        // Enforce single-active-featured constraint if the current one is being promoted to featured
        if (metaForm.featured) {
          return { ...p, featured: false };
        }
        return p;
      }
    });

    setPosts(updated);
    setSaveSuccess(true);
    setTimeout(() => setSaveSuccess(false), 3000);

    const activeSqlFeaturedUnfeature = metaForm.featured 
      ? `UPDATE wp_postmeta SET meta_value = '0' WHERE meta_key = '_weardale_featured_post' AND post_id != ${selectedSimPostId === 'p1' ? '101' : selectedSimPostId === 'p2' ? '102' : selectedSimPostId === 'p3' ? '103' : '104'};\n`
      : '';

    const sqlLog = `// TRACE: admin_post_save_post hook triggered for Post ID: ${selectedSimPostId === 'p1' ? '101' : selectedSimPostId === 'p2' ? '102' : selectedSimPostId === 'p3' ? '103' : '104'} ("${currentSimPost?.title}")
// Nonce checked successfully. Sanitizing meta inputs...
// wp_postmeta SQL queries executed:
UPDATE wp_postmeta SET meta_value = '${metaForm.strand}' WHERE meta_key = '_weardale_post_programme' AND post_id = ${selectedSimPostId === 'p1' ? '101' : selectedSimPostId === 'p2' ? '102' : selectedSimPostId === 'p3' ? '103' : '104'};
UPDATE wp_postmeta SET meta_value = '${metaForm.relatedEvent}' WHERE meta_key = '_weardale_related_event_id' AND post_id = ${selectedSimPostId === 'p1' ? '101' : selectedSimPostId === 'p2' ? '102' : selectedSimPostId === 'p3' ? '103' : '104'};
UPDATE wp_postmeta SET meta_value = '${metaForm.relatedDirectory}' WHERE meta_key = '_weardale_related_directory_id' AND post_id = ${selectedSimPostId === 'p1' ? '101' : selectedSimPostId === 'p2' ? '102' : selectedSimPostId === 'p3' ? '103' : '104'};
${activeSqlFeaturedUnfeature}UPDATE wp_postmeta SET meta_value = '${metaForm.featured ? '1' : '0'}' WHERE meta_key = '_weardale_featured_post' AND post_id = ${selectedSimPostId === 'p1' ? '101' : selectedSimPostId === 'p2' ? '102' : selectedSimPostId === 'p3' ? '103' : '104'};

// Database state synced. Redirection header status 302 to edit.php. Cache purged.`;

    setConsoleTraceLog(sqlLog);
  };
  
  // Custom states for interactive mock forms
  const [newsletterEmail, setNewsletterEmail] = useState('');
  const [newsletterSubscribed, setNewsletterSubscribed] = useState(false);
  
  const [contactForm, setContactForm] = useState({ name: '', email: '', message: '' });
  const [contactSubmitted, setContactSubmitted] = useState(false);

  const [volunteerForm, setVolunteerForm] = useState({ name: '', email: '', role: 'kitchen', notes: '' });
  const [volunteerSubmitted, setVolunteerSubmitted] = useState(false);

  // WordPress Console States
  const [selectedFile, setSelectedFile] = useState<WPFile>(wordpressFiles[0]);
  const [copiedFile, setCopiedFile] = useState(false);

  // Mock Event Editor States (Allows adding mock events in preview!)
  const [showEventModal, setShowEventModal] = useState(false);
  const [newEvent, setNewEvent] = useState({
    title: '',
    content: '',
    date: '2026-08-20',
    time: '11:00 AM - 2:00 PM',
    location: 'Stanhope Hub Rooms',
    cost: 'Free (Donations Welcome)',
    strand: 'creative' as 'cafe' | 'creative' | 'youth' | 'roots-shoots'
  });
  const [sqlQueryPreview, setSqlQueryPreview] = useState<string>('');

  const handleCreateEvent = (e: React.FormEvent) => {
    e.preventDefault();
    const created: WTEvent = {
      id: 'mock_' + Date.now(),
      ...newEvent
    };
    setEvents([created, ...events]);
    
    // Generate beautiful realistic WordPress SQL query to show in preview
    const query = `-- WordPress SQL Database Query Generated for Event
INSERT INTO \`wp_posts\` (\`post_author\`, \`post_date\`, \`post_content\`, \`post_title\`, \`post_name\`, \`post_type\`, \`post_status\`)
VALUES (1, NOW(), '${created.content.replace(/'/g, "\\'")}', '${created.title.replace(/'/g, "\\'")}', '${created.title.toLowerCase().replace(/[^a-z0-9]+/g, '-')}', 'weardale_event', 'publish');

SET @last_id = LAST_INSERT_ID();

INSERT INTO \`wp_postmeta\` (\`post_id\`, \`meta_key\`, \`meta_value\`) VALUES
(@last_id, '_event_date', '${created.date}'),
(@last_id, '_event_time', '${created.time}'),
(@last_id, '_event_location', '${created.location}'),
(@last_id, '_event_cost', '${created.cost}');`;

    setSqlQueryPreview(query);
    setShowEventModal(false);
    // Auto-scroll to Events block
    setTimeout(() => {
      document.getElementById('whats-happening')?.scrollIntoView({ behavior: 'smooth' });
    }, 100);
  };

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    setCopiedFile(true);
    setTimeout(() => setCopiedFile(false), 2000);
  };

  return (
    <div className="min-h-screen flex flex-col bg-[#F5F0E8] text-[#2C2C2A] antialiased">
      
      {/* 1. Header Navigation */}
      <header className="sticky top-0 z-50 bg-white border-b border-[#C4B89A] shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16 lg:h-20">
            
            {/* Branding Logo */}
            <div 
              onClick={() => { setActiveTab('home'); window.scrollTo({ top: 0, behavior: 'smooth' }); }} 
              className="flex items-center gap-2 lg:gap-3 cursor-pointer group"
              id="header_branding"
            >
              <div className="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-[#3B5C3A] flex items-center justify-center text-white font-display text-base lg:text-lg font-bold shadow-md transition-transform group-hover:scale-105">
                WT
              </div>
              <div>
                <h1 className="font-display text-base lg:text-xl text-[#3B5C3A] tracking-tight leading-none font-semibold">Weardale Together</h1>
                <p className="text-[10px] lg:text-xs text-gray-500 font-sans mt-0.5">Community Interest Company</p>
              </div>
            </div>

            {/* Desktop Navigation links */}
            <nav className="hidden lg:flex items-center gap-1 sm:gap-1.5">
              <button 
                onClick={() => { setActiveTab('home'); window.scrollTo({ top: 0, behavior: 'smooth' }); }} 
                className={`px-3 py-2 rounded-md font-semibold text-sm transition-all ${activeTab === 'home' ? 'text-[#3B5C3A] border-b-2 border-[#6B8F5E]' : 'text-[#2C2C2A] hover:bg-[#F5F0E8] hover:text-[#3B5C3A]'}`}
                id="nav_home"
              >
                Home
              </button>

              {/* Our Programmes Dropdown */}
              <div 
                className="relative"
                onMouseEnter={() => setProgrammesOpen(true)}
                onMouseLeave={() => setProgrammesOpen(false)}
              >
                <button 
                  className={`px-3 py-2 rounded-md font-semibold text-sm flex items-center gap-1 transition-all text-[#2C2C2A] hover:bg-[#F5F0E8] hover:text-[#3B5C3A] ${['cafe', 'creative', 'youth', 'roots-shoots'].includes(activeTab) ? 'text-[#3B5C3A] border-b-2 border-[#6B8F5E]' : ''}`}
                >
                  Our Programmes <ChevronDown className={`w-4 h-4 transition-transform duration-200 ${programmesOpen ? 'rotate-180' : ''}`} />
                </button>
                <AnimatePresence>
                  {programmesOpen && (
                    <motion.div 
                      initial={{ opacity: 0, y: 10 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0, y: 10 }}
                      className="absolute left-1/2 -translate-x-1/2 mt-1 w-56 bg-white border border-[#C4B89A] rounded-lg shadow-xl py-2 z-50"
                    >
                      <button 
                        onClick={() => { setActiveTab('cafe'); setProgrammesOpen(false); window.scrollTo({ top: 0, behavior: 'smooth' }); }}
                        className="w-full text-left px-4 py-2.5 text-sm font-medium hover:bg-[#F5F0E8] hover:text-[#3B5C3A] transition-colors flex items-center gap-2"
                      >
                        ☕ Root & Branch Café
                      </button>
                      <button 
                        onClick={() => { setActiveTab('youth'); setProgrammesOpen(false); window.scrollTo({ top: 0, behavior: 'smooth' }); }}
                        className="w-full text-left px-4 py-2.5 text-sm font-medium hover:bg-[#F5F0E8] hover:text-[#3B5C3A] transition-colors flex items-center gap-2"
                      >
                        🌲 Young People
                      </button>
                      <button 
                        onClick={() => { setActiveTab('creative'); setProgrammesOpen(false); window.scrollTo({ top: 0, behavior: 'smooth' }); }}
                        className="w-full text-left px-4 py-2.5 text-sm font-medium hover:bg-[#F5F0E8] hover:text-[#3B5C3A] transition-colors flex items-center gap-2"
                      >
                        🎨 Creative Arts
                      </button>
                      <button 
                        onClick={() => { setActiveTab('roots-shoots'); setProgrammesOpen(false); window.scrollTo({ top: 0, behavior: 'smooth' }); }}
                        className="w-full text-left px-4 py-2.5 text-sm font-medium hover:bg-[#F5F0E8] hover:text-[#3B5C3A] transition-colors flex items-center gap-2"
                      >
                        🧸 Roots & Shoots
                      </button>
                    </motion.div>
                  )}
                </AnimatePresence>
              </div>

              {/* High-Contrast What's On CTA */}
              <button 
                onClick={() => {
                  setActiveTab('home');
                  setTimeout(() => {
                    document.getElementById('whats-happening')?.scrollIntoView({ behavior: 'smooth' });
                  }, 150);
                }} 
                className="px-4 py-1.5 bg-[#3B5C3A] text-[#F5F0E8] hover:bg-[#6B8F5E] hover:text-white rounded-full font-bold text-sm shadow-sm hover:shadow-md transition-all ml-1"
                id="nav_whats_on"
              >
                What’s On
              </button>

              <button 
                onClick={() => setShowDirectoryModal(true)} 
                className="px-3 py-2 rounded-md font-semibold text-sm text-[#2C2C2A] hover:bg-[#F5F0E8] hover:text-[#3B5C3A] transition-all"
                id="nav_directory"
              >
                Community Directory
              </button>

              <button 
                onClick={() => setShowAboutModal(true)} 
                className="px-3 py-2 rounded-md font-semibold text-sm text-[#2C2C2A] hover:bg-[#F5F0E8] hover:text-[#3B5C3A] transition-all"
                id="nav_about"
              >
                About WT
              </button>

              {/* Get Involved Dropdown */}
              <div 
                className="relative"
                onMouseEnter={() => setInvolvedOpen(true)}
                onMouseLeave={() => setInvolvedOpen(false)}
              >
                <button 
                  className="px-3 py-2 rounded-md font-semibold text-sm flex items-center gap-1 transition-all text-[#2C2C2A] hover:bg-[#F5F0E8] hover:text-[#3B5C3A]"
                >
                  Get Involved <ChevronDown className={`w-4 h-4 transition-transform duration-200 ${involvedOpen ? 'rotate-180' : ''}`} />
                </button>
                <AnimatePresence>
                  {involvedOpen && (
                    <motion.div 
                      initial={{ opacity: 0, y: 10 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0, y: 10 }}
                      className="absolute left-1/2 -translate-x-1/2 mt-1 w-52 bg-white border border-[#C4B89A] rounded-lg shadow-xl py-2 z-50"
                    >
                      <button 
                        onClick={() => {
                          setInvolvedOpen(false);
                          setActiveTab('home');
                          setTimeout(() => {
                            document.getElementById('volunteer-form-section')?.scrollIntoView({ behavior: 'smooth' });
                          }, 150);
                        }}
                        className="w-full text-left px-4 py-2 text-sm font-medium hover:bg-[#F5F0E8] hover:text-[#3B5C3A] transition-colors"
                      >
                        Volunteer with Us
                      </button>
                      <button 
                        onClick={() => {
                          setInvolvedOpen(false);
                          setActiveTab('home');
                          setTimeout(() => {
                            document.getElementById('newsletter-section')?.scrollIntoView({ behavior: 'smooth' });
                          }, 150);
                        }}
                        className="w-full text-left px-4 py-2 text-sm font-medium hover:bg-[#F5F0E8] hover:text-[#3B5C3A] transition-colors"
                      >
                        Newsletter
                      </button>
                      <button 
                        onClick={() => {
                          setInvolvedOpen(false);
                          setActiveTab('home');
                          setTimeout(() => {
                            document.getElementById('contact-form-section')?.scrollIntoView({ behavior: 'smooth' });
                          }, 150);
                        }}
                        className="w-full text-left px-4 py-2 text-sm font-medium hover:bg-[#F5F0E8] hover:text-[#3B5C3A] transition-colors"
                      >
                        Contact Us
                      </button>
                    </motion.div>
                  )}
                </AnimatePresence>
              </div>
              
              <div className="w-[1px] h-6 bg-gray-300 mx-2"></div>
              
              <button 
                onClick={() => { setActiveTab('console'); window.scrollTo({ top: 0, behavior: 'smooth' }); }} 
                className={`px-4 py-1.5 rounded-full font-bold text-sm flex items-center gap-1.5 border transition-all ${activeTab === 'console' ? 'bg-zinc-800 text-zinc-100 border-zinc-800' : 'bg-transparent text-zinc-700 border-zinc-300 hover:bg-zinc-100'}`}
                id="nav_console"
              >
                <Code className="w-4 h-4" />
                WordPress Console
              </button>
            </nav>

            {/* Mobile Menu Toggle button */}
            <div className="flex lg:hidden items-center gap-2">
              <button 
                onClick={() => { setActiveTab('console'); window.scrollTo({ top: 0, behavior: 'smooth' }); }} 
                className={`p-2 rounded-full border transition-all ${activeTab === 'console' ? 'bg-zinc-800 text-zinc-100 border-zinc-800' : 'bg-transparent text-zinc-700 border-[#C4B89A] hover:bg-zinc-100'}`}
                title="WordPress Console"
              >
                <Code className="w-5 h-5" />
              </button>
              <button
                onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                className="p-2 rounded-md border border-[#C4B89A] text-[#2C2C2A] hover:bg-[#F5F0E8]"
                aria-label="Toggle navigation menu"
              >
                {mobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
              </button>
            </div>

          </div>
        </div>

        {/* Mobile Navigation Dropdown Panel */}
        <AnimatePresence>
          {mobileMenuOpen && (
            <motion.div
              initial={{ height: 0, opacity: 0 }}
              animate={{ height: 'auto', opacity: 1 }}
              exit={{ height: 0, opacity: 0 }}
              className="lg:hidden border-t border-[#C4B89A] bg-white overflow-hidden"
            >
              <div className="px-4 py-4 space-y-2 flex flex-col">
                <button
                  onClick={() => { setActiveTab('home'); setMobileMenuOpen(false); window.scrollTo({ top: 0, behavior: 'smooth' }); }}
                  className={`w-full text-left px-3 py-2.5 rounded-md font-semibold text-sm ${activeTab === 'home' ? 'bg-[#F5F0E8] text-[#3B5C3A]' : 'text-[#2C2C2A]'}`}
                >
                  Home
                </button>

                {/* Mobile Programmes Accordion */}
                <div>
                  <button
                    onClick={() => setMobileProgrammesOpen(!mobileProgrammesOpen)}
                    className="w-full text-left px-3 py-2.5 rounded-md font-semibold text-sm flex justify-between items-center text-[#2C2C2A]"
                  >
                    <span>Our Programmes</span>
                    <ChevronDown className={`w-4 h-4 transition-transform ${mobileProgrammesOpen ? 'rotate-180' : ''}`} />
                  </button>
                  {mobileProgrammesOpen && (
                    <div className="pl-6 pr-3 py-1 space-y-1 bg-[#F5F0E8]/50 border-l-2 border-[#C4B89A] ml-3">
                      <button
                        onClick={() => { setActiveTab('cafe'); setMobileMenuOpen(false); window.scrollTo({ top: 0, behavior: 'smooth' }); }}
                        className="w-full text-left py-2 text-sm font-medium text-[#2C2C2A] hover:text-[#3B5C3A]"
                      >
                        ☕ Root & Branch Café
                      </button>
                      <button
                        onClick={() => { setActiveTab('youth'); setMobileMenuOpen(false); window.scrollTo({ top: 0, behavior: 'smooth' }); }}
                        className="w-full text-left py-2 text-sm font-medium text-[#2C2C2A] hover:text-[#3B5C3A]"
                      >
                        🌲 Young People
                      </button>
                      <button
                        onClick={() => { setActiveTab('creative'); setMobileMenuOpen(false); window.scrollTo({ top: 0, behavior: 'smooth' }); }}
                        className="w-full text-left py-2 text-sm font-medium text-[#2C2C2A] hover:text-[#3B5C3A]"
                      >
                        🎨 Creative Arts
                      </button>
                      <button
                        onClick={() => { setActiveTab('roots-shoots'); setMobileMenuOpen(false); window.scrollTo({ top: 0, behavior: 'smooth' }); }}
                        className="w-full text-left py-2 text-sm font-medium text-[#2C2C2A] hover:text-[#3B5C3A]"
                      >
                        🧸 Roots & Shoots
                      </button>
                    </div>
                  )}
                </div>

                {/* Mobile What's On CTA */}
                <button
                  onClick={() => {
                    setMobileMenuOpen(false);
                    setActiveTab('home');
                    setTimeout(() => {
                      document.getElementById('whats-happening')?.scrollIntoView({ behavior: 'smooth' });
                    }, 200);
                  }}
                  className="w-full text-center py-2.5 bg-[#3B5C3A] text-white rounded-md font-bold text-sm hover:bg-[#6B8F5E] transition-colors"
                >
                  What’s On
                </button>

                <button
                  onClick={() => { setMobileMenuOpen(false); setShowDirectoryModal(true); }}
                  className="w-full text-left px-3 py-2.5 rounded-md font-semibold text-sm text-[#2C2C2A]"
                >
                  Community Directory
                </button>

                <button
                  onClick={() => { setMobileMenuOpen(false); setShowAboutModal(true); }}
                  className="w-full text-left px-3 py-2.5 rounded-md font-semibold text-sm text-[#2C2C2A]"
                >
                  About WT
                </button>

                {/* Mobile Get Involved Accordion */}
                <div>
                  <button
                    onClick={() => setMobileInvolvedOpen(!mobileInvolvedOpen)}
                    className="w-full text-left px-3 py-2.5 rounded-md font-semibold text-sm flex justify-between items-center text-[#2C2C2A]"
                  >
                    <span>Get Involved</span>
                    <ChevronDown className={`w-4 h-4 transition-transform ${mobileInvolvedOpen ? 'rotate-180' : ''}`} />
                  </button>
                  {mobileInvolvedOpen && (
                    <div className="pl-6 pr-3 py-1 space-y-1 bg-[#F5F0E8]/50 border-l-2 border-[#C4B89A] ml-3">
                      <button
                        onClick={() => {
                          setMobileMenuOpen(false);
                          setActiveTab('home');
                          setTimeout(() => {
                            document.getElementById('volunteer-form-section')?.scrollIntoView({ behavior: 'smooth' });
                          }, 200);
                        }}
                        className="w-full text-left py-2 text-sm font-medium text-[#2C2C2A]"
                      >
                        Volunteer with Us
                      </button>
                      <button
                        onClick={() => {
                          setMobileMenuOpen(false);
                          setActiveTab('home');
                          setTimeout(() => {
                            document.getElementById('newsletter-section')?.scrollIntoView({ behavior: 'smooth' });
                          }, 200);
                        }}
                        className="w-full text-left py-2 text-sm font-medium text-[#2C2C2A]"
                      >
                        Newsletter
                      </button>
                      <button
                        onClick={() => {
                          setMobileMenuOpen(false);
                          setActiveTab('home');
                          setTimeout(() => {
                            document.getElementById('contact-form-section')?.scrollIntoView({ behavior: 'smooth' });
                          }, 200);
                        }}
                        className="w-full text-left py-2 text-sm font-medium text-[#2C2C2A]"
                      >
                        Contact Us
                      </button>
                    </div>
                  )}
                </div>

              </div>
            </motion.div>
          )}
        </AnimatePresence>
      </header>

      {/* Main Container with AnimatePresence */}
      <main className="flex-grow">
        <AnimatePresence mode="wait">
          
          {/* ========================================================= */}
          {/* 2. MAIN HOMEPAGE TABS                                     */}
          {/* ========================================================= */}
          {activeTab === 'home' && (
            <motion.div
              key="home"
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              transition={{ duration: 0.3 }}
            >
              
              {/* Hero Banner Section */}
              <section className="relative overflow-hidden bg-white py-16 sm:py-24 border-b border-[#C4B89A]">
                <div className="absolute top-0 right-0 w-[500px] h-[500px] rounded-full bg-[#6B8F5E]/5 -mr-48 -mt-48 pointer-events-none"></div>
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                  <div className="max-w-3xl mx-auto text-center">
                    <span className="inline-block px-4 py-1.5 rounded-full bg-[#3B5C3A]/10 text-[#3B5C3A] text-sm font-bold tracking-tight mb-6">
                      Community at the heart
                    </span>
                    <h2 className="font-display text-4xl sm:text-5xl lg:text-6xl text-[#3B5C3A] leading-tight tracking-tight mb-6">
                      Living rurally shouldn't mean living without access to creativity, community, and connection.
                    </h2>
                    <p className="text-lg text-gray-600 leading-relaxed mb-10 max-w-2xl mx-auto">
                      Weardale Together is a grassroots Community Interest Company (CIC) founded in Stanhope, serving over 500 individuals annually across the North Pennines. Through food, arts, play, and outdoor adventure, we connect hearts and homes.
                    </p>
                    <div className="flex gap-4 justify-center flex-wrap">
                      <a href="#interactive-hub" className="px-6 py-3 rounded-full font-bold bg-[#3B5C3A] text-white shadow-md hover:bg-[#6B8F5E] transition-all">
                        Explore Our Strands
                      </a>
                      <button 
                        onClick={() => {
                          const contactBlock = document.getElementById('contact-form-section');
                          contactBlock?.scrollIntoView({ behavior: 'smooth' });
                        }}
                        className="px-6 py-3 rounded-full font-semibold border-2 border-[#3B5C3A] text-[#3B5C3A] hover:bg-[#3B5C3A]/5 transition-all"
                      >
                        Visit Stanhope Hub
                      </button>
                    </div>
                  </div>
                </div>
              </section>

              {/* Priority Feature: Interactive Hub-and-Spoke Section */}
              <section id="interactive-hub" className="py-20 bg-[#F5F0E8] border-b border-[#C4B89A]">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                  
                  <div className="text-center max-w-2xl mx-auto mb-12">
                    <h3 className="font-display text-3xl sm:text-4xl text-[#3B5C3A] mb-4">How We Connect</h3>
                    <p className="text-gray-600 leading-relaxed">
                      Weardale Together is the central umbrella organization. Our activities radiate outward into four core community strands. Click on any strand below to visit its cosy themed "room".
                    </p>
                    <div className="w-16 h-[2px] bg-[#3B5C3A] mx-auto mt-4"></div>
                  </div>

                  {/* Desktop Interactive SVG circular spokes (md & up) */}
                  <div className="hidden md:block relative w-full max-w-[800px] h-[500px] mx-auto bg-transparent">
                    
                    {/* Connecting lines SVG */}
                    <svg className="absolute inset-0 w-full h-full pointer-events-none" aria-hidden="true">
                      {/* Diagonal vectors */}
                      <line x1="220" y1="130" x2="400" y2="250" stroke="#C4B89A" strokeWidth="3" strokeDasharray="6 4" />
                      <line x1="580" y1="130" x2="400" y2="250" stroke="#C4B89A" strokeWidth="3" strokeDasharray="6 4" />
                      <line x1="220" y1="370" x2="400" y2="250" stroke="#C4B89A" strokeWidth="3" strokeDasharray="6 4" />
                      <line x1="580" y1="370" x2="400" y2="250" stroke="#C4B89A" strokeWidth="3" strokeDasharray="6 4" />
                    </svg>

                    {/* Central Hub Brand Node */}
                    <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-20">
                      <div className="w-48 h-48 rounded-full bg-[#3B5C3A] border-4 border-white text-[#F5F0E8] flex flex-col items-center justify-center text-center p-6 shadow-xl">
                        <span className="font-display text-xl leading-tight font-bold">Weardale Together</span>
                        <div className="w-12 h-[1px] bg-[#C4B89A] my-3"></div>
                        <span className="text-[10px] uppercase tracking-widest font-sans text-[#C4B89A]">Community CIC</span>
                      </div>
                    </div>

                    {/* Node 1: Café (Top Left) */}
                    <button 
                      onClick={() => { setActiveTab('cafe'); window.scrollTo(0, 0); }}
                      className="absolute top-[50px] left-[50px] z-10 w-44 h-44 rounded-full bg-white border-3 border-[#C4956A] flex flex-col items-center justify-center text-center p-4 shadow-lg hover:scale-105 hover:border-[#3B5C3A] transition-all cursor-pointer group"
                    >
                      <Coffee className="w-8 h-8 text-[#C4956A] mb-2 group-hover:rotate-12 transition-transform" />
                      <span className="font-display text-base text-[#9E6B3E] font-semibold leading-tight">Root & Branch Café</span>
                      <span className="text-[10px] text-gray-500 uppercase tracking-wider mt-1 font-sans">Cosy & Welcoming</span>
                    </button>

                    {/* Node 2: Creative Arts (Top Right) */}
                    <button 
                      onClick={() => { setActiveTab('creative'); window.scrollTo(0, 0); }}
                      className="absolute top-[50px] right-[50px] z-10 w-44 h-44 rounded-full bg-white border-3 border-[#E8A020] flex flex-col items-center justify-center text-center p-4 shadow-lg hover:scale-105 hover:border-[#3B5C3A] transition-all cursor-pointer group"
                    >
                      <Palette className="w-8 h-8 text-[#E8A020] mb-2 group-hover:rotate-12 transition-transform" />
                      <span className="font-display text-base text-[#BA7D0C] font-semibold leading-tight">Creative Arts</span>
                      <span className="text-[10px] text-gray-500 uppercase tracking-wider mt-1 font-sans">Botanical & Seasonal</span>
                    </button>

                    {/* Node 3: Youth Programme (Bottom Left) */}
                    <button 
                      onClick={() => { setActiveTab('youth'); window.scrollTo(0, 0); }}
                      className="absolute bottom-[50px] left-[50px] z-10 w-44 h-44 rounded-full bg-white border-3 border-[#E8962A] flex flex-col items-center justify-center text-center p-4 shadow-lg hover:scale-105 hover:border-[#3B5C3A] transition-all cursor-pointer group"
                    >
                      <Trees className="w-8 h-8 text-[#E8962A] mb-2 group-hover:scale-110 transition-transform" />
                      <span className="font-display text-base text-[#E8962A] font-semibold leading-tight">Young People</span>
                      <span className="text-[10px] text-gray-500 uppercase tracking-wider mt-1 font-sans">Bold & Energetic</span>
                    </button>

                    {/* Node 4: Roots & Shoots (Bottom Right) */}
                    <button 
                      onClick={() => { setActiveTab('roots-shoots'); window.scrollTo(0, 0); }}
                      className="absolute bottom-[50px] right-[50px] z-10 w-44 h-44 rounded-full bg-white border-3 border-[#D4826A] flex flex-col items-center justify-center text-center p-4 shadow-lg hover:scale-105 hover:border-[#3B5C3A] transition-all cursor-pointer group"
                    >
                      <Baby className="w-8 h-8 text-[#D4826A] mb-2 group-hover:scale-105 transition-transform" />
                      <span className="font-display text-base text-[#B2583E] font-semibold leading-tight">Roots & Shoots</span>
                      <span className="text-[10px] text-gray-500 uppercase tracking-wider mt-1 font-sans">Soft & Unhurried</span>
                    </button>

                  </div>

                  {/* Mobile-friendly fallback responsive stacking menu */}
                  <div className="md:hidden max-w-sm mx-auto flex flex-col gap-4">
                    <div className="bg-[#3B5C3A] text-white text-center p-5 rounded-2xl shadow-md">
                      <h4 className="font-display text-lg">Weardale Together</h4>
                      <span className="text-[10px] uppercase tracking-widest text-[#C4B89A] block mt-1 font-sans">Grassroots CIC</span>
                    </div>

                    <button 
                      onClick={() => { setActiveTab('cafe'); window.scrollTo(0, 0); }}
                      className="flex items-center gap-4 bg-white p-4 rounded-xl shadow-sm border-l-4 border-[#C4956A]"
                    >
                      <Coffee className="w-6 h-6 text-[#C4956A]" />
                      <div className="text-left">
                        <span className="font-display font-bold text-gray-800 block text-sm">Root & Branch Café</span>
                        <span className="text-xs text-gray-500">Food-led, cosy community hospitality</span>
                      </div>
                    </button>

                    <button 
                      onClick={() => { setActiveTab('creative'); window.scrollTo(0, 0); }}
                      className="flex items-center gap-4 bg-white p-4 rounded-xl shadow-sm border-l-4 border-[#E8A020]"
                    >
                      <Palette className="w-6 h-6 text-[#E8A020]" />
                      <div className="text-left">
                        <span className="font-display font-bold text-gray-800 block text-sm">Creative Arts</span>
                        <span className="text-xs text-gray-500">Earthy, natural workshops & botany</span>
                      </div>
                    </button>

                    <button 
                      onClick={() => { setActiveTab('youth'); window.scrollTo(0, 0); }}
                      className="flex items-center gap-4 bg-white p-4 rounded-xl shadow-sm border-l-4 border-[#E8962A]"
                    >
                      <Trees className="w-6 h-6 text-[#E8962A]" />
                      <div className="text-left">
                        <span className="font-display font-bold text-gray-800 block text-sm">Young People</span>
                        <span className="text-xs text-gray-500">Forest schools & adventurous learning</span>
                      </div>
                    </button>

                    <button 
                      onClick={() => { setActiveTab('roots-shoots'); window.scrollTo(0, 0); }}
                      className="flex items-center gap-4 bg-white p-4 rounded-xl shadow-sm border-l-4 border-[#D4826A]"
                    >
                      <Baby className="w-6 h-6 text-[#D4826A]" />
                      <div className="text-left">
                        <span className="font-display font-bold text-gray-800 block text-sm">Roots & Shoots</span>
                        <span className="text-xs text-gray-500">Gentle family playrooms & support</span>
                      </div>
                    </button>
                  </div>

                </div>
              </section>

              {/* What's Happening This Week Section (WT Events Listing) */}
              <section id="whats-happening" className="py-20 bg-white border-b border-[#C4B89A]">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                  
                  <div className="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-12 gap-4">
                    <div>
                      <span className="px-3 py-1 text-xs font-bold rounded-full bg-[#D4826A]/15 text-[#D4826A] uppercase tracking-wider mb-2 inline-block">
                        Our Activities
                      </span>
                      <h3 className="font-display text-3xl sm:text-4xl text-[#3B5C3A] font-normal">What's Happening This Week</h3>
                    </div>
                    <div className="flex gap-2">
                      {/* Dynamic interactive feature: Add Mock Event! */}
                      <button 
                        onClick={() => setShowEventModal(true)}
                        className="px-4 py-2 rounded-full font-bold bg-[#3B5C3A] text-white flex items-center gap-1.5 hover:bg-[#6B8F5E] transition-all cursor-pointer text-sm shadow-sm"
                      >
                        <PlusCircle className="w-4 h-4" />
                        Create Mock Event
                      </button>
                    </div>
                  </div>

                  {sqlQueryPreview && (
                    <div className="mb-8 p-4 bg-zinc-800 text-zinc-300 rounded-lg font-mono text-xs overflow-x-auto border border-zinc-700 shadow-inner">
                      <p className="text-emerald-400 font-bold mb-2">⚡ WordPress Database Insert SQL Triggered:</p>
                      <pre>{sqlQueryPreview}</pre>
                    </div>
                  )}

                  <div className="grid grid-1 md:grid-cols-3 gap-8">
                    {events.map((evt) => (
                      <article key={evt.id} className="bg-white border border-[#C4B89A] rounded-xl p-6 flex flex-col hover:-translate-y-1 hover:shadow-lg transition-all border-t-4 border-t-[#3B5C3A]">
                        <div className="mb-3">
                          <span className={`px-2.5 py-0.5 text-[10px] font-bold rounded-full uppercase tracking-wider ${
                            evt.strand === 'cafe' ? 'bg-[#C4956A]/10 text-[#C4956A]' :
                            evt.strand === 'creative' ? 'bg-[#E8A020]/10 text-[#E8A020]' :
                            evt.strand === 'youth' ? 'bg-[#E8962A]/10 text-[#E8962A]' :
                            'bg-[#D4826A]/10 text-[#D4826A]'
                          }`}>
                            {evt.strand === 'cafe' ? '☕ Café' :
                             evt.strand === 'creative' ? '🎨 Creative' :
                             evt.strand === 'youth' ? '🌲 Youth' :
                             '🧸 Playroom'}
                          </span>
                        </div>
                        <h4 className="font-display text-xl text-gray-900 mb-4 min-h-[50px]">{evt.title}</h4>
                        
                        <div className="space-y-2 text-sm text-gray-600 mb-6 flex-grow">
                          <div className="flex items-center gap-2">
                            <span>📅</span>
                            <strong>{new Date(evt.date).toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}</strong>
                          </div>
                          <div className="flex items-center gap-2">
                            <Clock className="w-4 h-4 text-gray-400" />
                            <span>{evt.time}</span>
                          </div>
                          <div className="flex items-center gap-2">
                            <MapPin className="w-4 h-4 text-gray-400" />
                            <span>{evt.location}</span>
                          </div>
                          <div className="flex items-center gap-2">
                            <DollarSign className="w-4 h-4 text-gray-400" />
                            <span>{evt.cost}</span>
                          </div>
                        </div>

                        <p className="text-sm text-gray-500 leading-relaxed mb-6 line-clamp-3">
                          {evt.content}
                        </p>

                        <button 
                          onClick={() => {
                            alert(`You are viewing details for "${evt.title}". Under WordPress, this triggers single-weardale_event.php.`);
                          }}
                          className="px-4 py-2 rounded-full border border-gray-300 text-gray-700 hover:border-[#3B5C3A] hover:text-[#3B5C3A] transition-all font-semibold text-sm self-start"
                        >
                          View Details
                        </button>
                      </article>
                    ))}
                  </div>

                  {/* Signposting to Dale-wide calendar */}
                  <div className="mt-12 p-6 bg-[#3B5C3A]/5 border border-[#3B5C3A]/15 rounded-xl flex flex-col md:flex-row justify-between items-center gap-6">
                    <div className="max-w-2xl">
                      <h4 className="font-display text-lg text-[#3B5C3A] mb-1">Looking for wider dale-wide events?</h4>
                      <p className="text-sm text-gray-600">
                        WT website strictly tracks our local direct program schedules. For comprehensive local directories, public map pins, and community event listings across the Pennines, visit the standalone <strong className="text-[#3B5C3A]">Weardale Places & What's On</strong> platform.
                      </p>
                    </div>
                    <a href="https://weardaleplaces.org.uk" target="_blank" rel="noreferrer" className="px-5 py-2.5 rounded-full font-bold bg-[#3B5C3A] text-white text-sm hover:bg-[#6B8F5E] transition-all whitespace-nowrap shadow-sm">
                      Visit Weardale Places &rarr;
                    </a>
                  </div>

                </div>
              </section>

              {/* Programme Strands Bento-Style Section */}
              <section className="py-20 bg-[#F5F0E8] border-b border-[#C4B89A]">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                  
                  <div className="text-center max-w-2xl mx-auto mb-16">
                    <span className="px-3 py-1 text-xs font-bold rounded-full bg-[#3B5C3A]/10 text-[#3B5C3A] uppercase tracking-wider mb-2 inline-block">
                      Our Strands
                    </span>
                    <h3 className="font-display text-3xl sm:text-4xl text-[#3B5C3A]">Step Into Our Different Rooms</h3>
                    <p className="text-gray-600 mt-2">
                      Each activity we lead is custom designed to have its own flavor, layout, and visual identity.
                    </p>
                  </div>

                  <div className="grid grid-1 md:grid-cols-2 gap-8">
                    
                    {/* Bento Card 1: Cafe */}
                    <div className="bg-white rounded-2xl border border-[#C4B89A] p-8 flex flex-col shadow-sm border-t-4 border-t-[#C4956A]">
                      <div className="flex justify-between items-start mb-6">
                        <div className="w-12 h-12 bg-[#C4956A]/10 rounded-full flex items-center justify-center text-[#C4956A] text-2xl font-bold">
                          ☕
                        </div>
                        <span className="text-xs font-bold tracking-wider text-[#C4956A] uppercase">Root & Branch</span>
                      </div>
                      <h4 className="font-display text-2xl text-[#9E6B3E] mb-3">Root & Branch Café</h4>
                      <p className="text-gray-600 leading-relaxed mb-6 flex-grow">
                        Cosy sandy warmth, slow fermented sourdough breads, hot soups, and polaroid food photography. The café sits at the heart of our Stanhope facility, offering a welcoming place for hikers and isolated neighbors alike.
                      </p>
                      <button 
                        onClick={() => { setActiveTab('cafe'); window.scrollTo(0, 0); }}
                        className="px-5 py-2.5 rounded-full font-bold bg-[#C4956A] text-white hover:bg-[#b07e54] transition-all text-sm self-start"
                      >
                        Visit the Café room
                      </button>
                    </div>

                    {/* Bento Card 2: Creative */}
                    <div className="bg-white rounded-2xl border border-[#C4B89A] p-8 flex flex-col shadow-sm border-t-4 border-t-[#E8A020]">
                      <div className="flex justify-between items-start mb-6">
                        <div className="w-12 h-12 bg-[#E8A020]/10 rounded-full flex items-center justify-center text-[#E8A020] text-2xl font-bold">
                          🎨
                        </div>
                        <span className="text-xs font-bold tracking-wider text-[#E8A020] uppercase">Creative Roots</span>
                      </div>
                      <h4 className="font-display text-2xl text-[#BA7D0C] mb-3">Creative Arts</h4>
                      <p className="text-gray-600 leading-relaxed mb-6 flex-grow">
                        Hand-drawn botanical ink sketches and seasonal warm color washes. This room hosts heritage crafts, block printing, and local history panels designed specifically for people who "don't think of themselves as creative".
                      </p>
                      <button 
                        onClick={() => { setActiveTab('creative'); window.scrollTo(0, 0); }}
                        className="px-5 py-2.5 rounded-full font-bold bg-[#E8A020] text-white hover:bg-[#d18e15] transition-all text-sm self-start"
                      >
                        Explore Crafts room
                      </button>
                    </div>

                    {/* Bento Card 3: Youth */}
                    <div className="bg-white rounded-2xl border border-[#C4B89A] p-8 flex flex-col shadow-sm border-t-4 border-t-[#E8962A]">
                      <div className="flex justify-between items-start mb-6">
                        <div className="w-12 h-12 bg-[#E8962A]/10 rounded-full flex items-center justify-center text-[#E8962A] text-2xl font-bold">
                          🌲
                        </div>
                        <span className="text-xs font-bold tracking-wider text-[#E8962A] uppercase">Young People</span>
                      </div>
                      <h4 className="font-display text-2xl text-[#E8962A] mb-3">Youth & Forest School</h4>
                      <p className="text-gray-600 leading-relaxed mb-6 flex-grow">
                        Bold, high-contrast collage styles and energetic yellow-green layouts. A vibrant, loud space belonging 100% to local teenagers. Includes woodcraft skills, mud-kitchen building, and team camps in the North Pennines.
                      </p>
                      <button 
                        onClick={() => { setActiveTab('youth'); window.scrollTo(0, 0); }}
                        className="px-5 py-2.5 rounded-full font-bold bg-[#E8962A] text-white hover:bg-[#cf831f] transition-all text-sm self-start"
                      >
                        See Youth room
                      </button>
                    </div>

                    {/* Bento Card 4: Roots & Shoots */}
                    <div className="bg-white rounded-2xl border border-[#C4B89A] p-8 flex flex-col shadow-sm border-t-4 border-t-[#D4826A]">
                      <div className="flex justify-between items-start mb-6">
                        <div className="w-12 h-12 bg-[#D4826A]/10 rounded-full flex items-center justify-center text-[#D4826A] text-2xl font-bold">
                          🧸
                        </div>
                        <span className="text-xs font-bold tracking-wider text-[#D4826A] uppercase">Roots & Shoots</span>
                      </div>
                      <h4 className="font-display text-2xl text-[#B2583E] mb-3">Roots & Shoots</h4>
                      <p className="text-gray-600 leading-relaxed mb-6 flex-grow">
                        Soft terracotta pot drawings, baby-pinks, and gentle sage greens. A quiet, unhurried, carpeted play area for babies, toddlers, and parent-carers to share support circles and slow morning routines.
                      </p>
                      <button 
                        onClick={() => { setActiveTab('roots-shoots'); window.scrollTo(0, 0); }}
                        className="px-5 py-2.5 rounded-full font-bold bg-[#D4826A] text-white hover:bg-[#bd6c54] transition-all text-sm self-start"
                      >
                        Step into Playroom
                      </button>
                    </div>

                  </div>

                </div>
              </section>

              {/* Latest News & Stories Section */}
              <section className="py-20 bg-white border-b border-[#C4B89A]">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                  
                  <div className="text-center max-w-2xl mx-auto mb-12">
                    <span className="px-3 py-1 text-xs font-bold rounded-full bg-[#3B5C3A]/10 text-[#3B5C3A] uppercase tracking-wider mb-2 inline-block">
                      Community Journal
                    </span>
                    <h3 className="font-display text-3xl sm:text-4xl text-[#3B5C3A]">Stories From Around Weardale</h3>
                    <p className="text-gray-600 mt-2">
                      Catch up on news, craft completions, and food reviews directly from our hubs.
                    </p>
                  </div>

                  {/* LIVE WORDPRESS METADATA SIMULATOR */}
                  <div className="bg-zinc-900 text-zinc-100 rounded-2xl p-6 sm:p-8 mb-12 border border-zinc-800 shadow-xl" id="wordpress-metadata-simulator">
                    <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                      <div>
                        <div className="flex items-center gap-1.5 mb-1">
                          <span className="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                          <span className="text-xs font-mono font-bold uppercase tracking-wider text-emerald-400">WordPress admin_init & meta-save Simulation</span>
                        </div>
                        <h4 className="font-display text-2xl text-white">Story Editorial Metadata Box (Simulator)</h4>
                        <p className="text-xs text-zinc-400 leading-relaxed mt-1">
                          Select any published post to view and update its real WordPress custom post meta (linked to <code className="text-emerald-400">news-meta.php</code> save-hook logic).
                        </p>
                      </div>
                      <div className="px-3 py-1 bg-zinc-800 border border-zinc-700 text-[11px] text-zinc-300 font-mono rounded">
                        wp_postmeta synced
                      </div>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                      {/* Left Side: Selected Post Selector */}
                      <div className="bg-zinc-950 p-5 rounded-xl border border-zinc-850">
                        <label className="block text-xs font-mono uppercase tracking-wider text-zinc-400 mb-2 font-bold">1. Select Story to Edit</label>
                        <select 
                          value={selectedSimPostId}
                          onChange={(e) => {
                            const pId = e.target.value;
                            setSelectedSimPostId(pId);
                            const matched = posts.find(p => p.id === pId);
                            if (matched) {
                              setMetaForm({
                                featured: matched.featured || false,
                                strand: matched.strand || '',
                                relatedEvent: matched.relatedEvent || '',
                                relatedDirectory: matched.relatedDirectory || ''
                              });
                            }
                          }}
                          className="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-1 focus:ring-emerald-400 focus:outline-none mb-4"
                        >
                          {posts.map(p => (
                            <option key={p.id} value={p.id}>
                              {p.title} {p.featured ? '(★ Featured)' : ''}
                            </option>
                          ))}
                        </select>

                        <div className="text-xs text-zinc-500 space-y-1.5 bg-zinc-900/40 p-3 rounded-lg border border-zinc-850">
                          <p><strong>Title:</strong> {currentSimPost?.title}</p>
                          <p><strong>Published Date:</strong> {currentSimPost?.date}</p>
                          <p><strong>Author:</strong> {currentSimPost?.author}</p>
                          <p><strong>Strand Page:</strong> <span className="font-semibold text-emerald-400">/{currentSimPost?.strand || 'none'}/</span></p>
                        </div>
                      </div>

                      {/* Middle: WordPress Editorial Metadata Meta Fields */}
                      <div className="lg:col-span-2 bg-zinc-950 p-5 rounded-xl border border-zinc-850">
                        <span className="block text-xs font-mono uppercase tracking-wider text-zinc-400 mb-4 font-bold">
                          2. Story Custom Meta Fields (Story Editorial Metadata)
                        </span>

                        <div className="space-y-4">
                          {/* Option: Promote to Featured Story */}
                          <div className="flex items-start gap-3 bg-zinc-900/50 p-3 rounded-lg border border-zinc-800/60 hover:bg-zinc-900 transition-all">
                            <input 
                              type="checkbox"
                              id="meta_featured"
                              checked={metaForm.featured}
                              onChange={(e) => setMetaForm({ ...metaForm, featured: e.target.checked })}
                              className="mt-1 w-4 h-4 text-emerald-600 border-zinc-750 bg-zinc-900 rounded focus:ring-emerald-500 cursor-pointer"
                            />
                            <label htmlFor="meta_featured" className="text-xs font-sans text-zinc-300 leading-normal cursor-pointer">
                              <strong className="block text-white font-semibold text-sm">Promote to Featured Story</strong>
                              When enabled, this story takes the prominent Spotlight position at the top of the Our Journal archive and Homepage grid, and is excluded from the main posts grid.
                            </label>
                          </div>

                          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {/* Option: Associated Programme Strand */}
                            <div>
                              <label className="block text-[11px] font-mono text-zinc-400 mb-1">Associated Programme Strand</label>
                              <select
                                value={metaForm.strand}
                                onChange={(e) => setMetaForm({ ...metaForm, strand: e.target.value })}
                                className="w-full bg-zinc-900 border border-zinc-750 rounded px-2.5 py-1.5 text-xs text-white focus:outline-none focus:ring-1 focus:ring-emerald-400"
                              >
                                <option value="">-- Select Strand --</option>
                                <option value="cafe">Root & Branch Café</option>
                                <option value="creative">Creative Arts</option>
                                <option value="youth">Young People</option>
                                <option value="roots-shoots">Roots & Shoots</option>
                              </select>
                            </div>

                            {/* Option: Related Community Event */}
                            <div>
                              <label className="block text-[11px] font-mono text-zinc-400 mb-1">Related Community Event</label>
                              <select
                                value={metaForm.relatedEvent}
                                onChange={(e) => setMetaForm({ ...metaForm, relatedEvent: e.target.value })}
                                className="w-full bg-zinc-900 border border-zinc-750 rounded px-2.5 py-1.5 text-xs text-white focus:outline-none focus:ring-1 focus:ring-emerald-400"
                              >
                                <option value="">-- Select Event --</option>
                                <option value="Summer Clay Sculpting">Summer Clay Sculpting</option>
                                <option className="italic text-emerald-400 font-bold" value="Andy's Event">Andy's Event</option>
                                <option value="Forest School: Tree Discovery">Forest School: Tree Discovery</option>
                                <option value="Senior Soup & Story Exchange">Senior Soup & Story Exchange</option>
                              </select>
                            </div>

                            {/* Option: Related Directory Listing */}
                            <div>
                              <label className="block text-[11px] font-mono text-zinc-400 mb-1">Related Directory Listing</label>
                              <select
                                value={metaForm.relatedDirectory}
                                onChange={(e) => setMetaForm({ ...metaForm, relatedDirectory: e.target.value })}
                                className="w-full bg-zinc-900 border border-zinc-750 rounded px-2.5 py-1.5 text-xs text-white focus:outline-none focus:ring-1 focus:ring-emerald-400"
                              >
                                <option value="">-- Select Directory Listing --</option>
                                <option value="The Stanhope Artisan Bakery">The Stanhope Artisan Bakery</option>
                                <option className="italic text-[#BA7D0C] font-bold" value="Weardale Creative Makers Guild">Weardale Creative Makers Guild</option>
                                <option value="Upper Weardale Woodcarving Club">Upper Weardale Woodcarving Club</option>
                              </select>
                            </div>
                          </div>

                          {/* Save & Output Block */}
                          <div className="flex flex-col sm:flex-row sm:items-center justify-between pt-2 gap-4 border-t border-zinc-800">
                            <button 
                              type="button"
                              onClick={handleSaveMetaForm}
                              className="px-5 py-2.5 rounded-full bg-emerald-500 text-black font-black text-xs hover:bg-emerald-400 transition-all flex items-center justify-center gap-1 cursor-pointer self-start sm:self-center"
                            >
                              Save Editorial Metadata
                            </button>

                            {saveSuccess && (
                              <span className="text-[11px] font-mono text-emerald-400 font-semibold flex items-center gap-1 animate-pulse">
                                ✓ Metadata saved! Database wp_postmeta updated & cached.
                              </span>
                            )}
                          </div>

                        </div>
                      </div>
                    </div>

                    {/* Simulation Console Trace logs for educational visualizer */}
                    <div className="mt-6 border-t border-zinc-800/80 pt-4 font-mono text-[10px] text-zinc-500">
                      <span className="text-zinc-400 block mb-1">WP_Query & Save Hooks execution trace:</span>
                      <pre className="bg-zinc-950 p-3 rounded border border-zinc-850 text-emerald-400/80 leading-relaxed overflow-x-auto select-all max-h-[140px]">
                        {consoleTraceLog || `// Waiting for metadata saves... Selecting 'A SONG for Weardale' and saving updates triggers the save_post PHP hooks and SQL single-active-featured constraint.`}
                      </pre>
                    </div>
                  </div>

                  {/* Spotlight Banner (Featured Story) */}
                  {posts.find(p => p.featured) && (
                    (() => {
                      const featuredPost = posts.find(p => p.featured)!;
                      return (
                        <article className="bg-white border-2 border-[#C4B89A] rounded-2xl overflow-hidden shadow-sm p-8 mb-12 grid grid-cols-1 md:grid-cols-2 gap-8 items-center border-t-4 border-t-[#E8A020]">
                          <div>
                            <div className="flex gap-2 items-center mb-3">
                              <span className="px-2.5 py-0.5 text-[10px] font-bold rounded bg-[#BA7D0C] text-white uppercase tracking-wider">
                                Featured Story
                              </span>
                              {featuredPost.strand && (
                                <span className="px-2.5 py-0.5 text-[10px] font-bold rounded bg-[#3B5C3A]/10 text-[#3B5C3A] uppercase tracking-wider">
                                  {featuredPost.strand === 'creative' ? '🎨 Creative' : featuredPost.strand === 'cafe' ? '☕ Café' : featuredPost.strand === 'youth' ? '🌲 Youth' : '🧸 Playroom'}
                                </span>
                              )}
                            </div>
                            <h4 className="font-display text-3xl text-gray-900 mb-3 tracking-tight leading-snug">{featuredPost.title}</h4>
                            <p className="text-sm text-gray-500 font-mono mb-4">{featuredPost.date} &bull; by {featuredPost.author}</p>
                            <p className="text-gray-600 text-sm leading-relaxed mb-6">{featuredPost.excerpt}</p>
                            
                            <div className="space-y-2">
                              {featuredPost.relatedEvent && (
                                <div className="text-xs text-gray-600 flex items-center gap-1.5 bg-[#F5F0E8] px-3 py-1.5 rounded-lg border border-[#C4B89A]/40 self-start w-fit">
                                  <span>📅 Related Activity:</span> <strong className="text-gray-800 font-bold">{featuredPost.relatedEvent}</strong>
                                </div>
                              )}
                              {featuredPost.relatedDirectory && (
                                <div className="text-xs text-gray-600 flex items-center gap-1.5 bg-[#F5F0E8] px-3 py-1.5 rounded-lg border border-[#C4B89A]/40 self-start w-fit">
                                  <span>📁 Directory Listing:</span> <strong className="text-gray-800 font-bold">{featuredPost.relatedDirectory}</strong>
                                </div>
                              )}
                            </div>
                            
                            <button 
                              onClick={() => {
                                let alertMsg = `Story: "${featuredPost.title}"\n\nContent:\n${featuredPost.content}`;
                                if (featuredPost.relatedEvent) alertMsg += `\n\n[Community Connection: Event] ${featuredPost.relatedEvent}`;
                                if (featuredPost.relatedDirectory) alertMsg += `\n[Community Connection: Directory] ${featuredPost.relatedDirectory}`;
                                alert(alertMsg);
                              }}
                              className="mt-6 px-6 py-3 rounded-full bg-[#3B5C3A] text-white font-bold hover:bg-[#6B8F5E] transition-all text-xs cursor-pointer shadow-sm"
                            >
                              Read Spotlight Story &rarr;
                            </button>
                          </div>
                          
                          <div className="bg-[#F5F0E8] border border-[#C4B89A] rounded-xl p-8 text-center flex flex-col items-center justify-center min-h-[260px] relative">
                            <span className="text-5xl mb-3">📰</span>
                            <h5 className="font-display font-semibold text-gray-800 text-sm mb-1">Spotlight Editorial Feature</h5>
                            <p className="text-xs text-gray-500 max-w-[240px]">This story is promoted across the homepage and journal archive with full related connections.</p>
                            <span className="absolute bottom-3 text-[10px] font-mono text-gray-400">Template: home.php (Featured Story Query)</span>
                          </div>
                        </article>
                      );
                    })()
                  )}

                  <div className="grid grid-1 md:grid-cols-3 gap-8">
                    {posts.filter(p => !p.featured).map((post) => (
                      <article key={post.id} className="bg-white border border-[#C4B89A] rounded-xl overflow-hidden shadow-sm flex flex-col hover:-translate-y-1 transition-all">
                        <div className="p-6 flex-grow flex flex-col">
                          <div className="text-xs text-gray-400 font-mono mb-2 flex justify-between items-center">
                            <span>{post.date} &bull; by {post.author}</span>
                            {post.strand && (
                              <span className="text-[10px] uppercase font-bold text-[#3B5C3A] bg-[#3B5C3A]/5 px-1.5 py-0.5 rounded">
                                {post.strand}
                              </span>
                            )}
                          </div>
                          <h4 className="font-display text-lg text-gray-900 mb-3 min-h-[44px] tracking-tight">{post.title}</h4>
                          <p className="text-sm text-gray-500 leading-relaxed mb-6 flex-grow">{post.excerpt}</p>
                          
                          <div className="space-y-1.5 mb-4">
                            {post.relatedEvent && (
                              <div className="text-[10px] text-gray-600 bg-amber-50 border border-amber-200/50 px-2 py-0.5 rounded w-fit">
                                📅 {post.relatedEvent}
                              </div>
                            )}
                            {post.relatedDirectory && (
                              <div className="text-[10px] text-gray-600 bg-[#D4826A]/5 border border-[#D4826A]/10 px-2 py-0.5 rounded w-fit">
                                📁 {post.relatedDirectory}
                              </div>
                            )}
                          </div>

                          <button 
                            onClick={() => {
                              let alertMsg = `Story: "${post.title}"\n\nContent:\n${post.content}`;
                              if (post.relatedEvent) alertMsg += `\n\n[Community Connection: Event] ${post.relatedEvent}`;
                              if (post.relatedDirectory) alertMsg += `\n[Community Connection: Directory] ${post.relatedDirectory}`;
                              alert(alertMsg);
                            }}
                            className="text-sm font-bold text-[#3B5C3A] hover:text-[#6B8F5E] self-start flex items-center gap-1 cursor-pointer"
                          >
                            Read Full Story <ChevronRight className="w-4 h-4" />
                          </button>
                        </div>
                      </article>
                    ))}
                  </div>

                </div>
              </section>

              {/* Volunteer Highlight CTA Banner */}
              <section className="py-12 bg-white border-b border-[#C4B89A]">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                  <div className="bg-[#3B5C3A] text-[#F5F0E8] rounded-2xl p-8 sm:p-12 shadow-lg grid grid-1 lg:grid-cols-3 gap-8 items-center relative overflow-hidden">
                    <div className="absolute -bottom-24 -left-24 w-60 h-60 rounded-full bg-[#C4956A]/10 pointer-events-none"></div>
                    <div className="lg:col-span-2">
                      <span className="text-xs font-bold uppercase tracking-widest text-[#C4B89A] bg-white/10 px-3 py-1 rounded-full mb-4 inline-block">
                        Join the Team
                      </span>
                      <h3 className="font-display text-3xl sm:text-4xl text-white mb-4">Share Your Time & Warmth with Weardale</h3>
                      <p className="text-base text-[#F5F0E8]/90 leading-relaxed">
                        Weardale Together is built on neighbors helping neighbors. Whether cooking with Cheryl, leading craft groups, driving residents, or helping with childcare playrooms, your hands and heart are valued here.
                      </p>
                    </div>
                    <div className="flex flex-col gap-3 sm:items-end justify-center">
                      <button 
                        onClick={() => {
                          const vBlock = document.getElementById('volunteer-form-section');
                          vBlock?.scrollIntoView({ behavior: 'smooth' });
                        }}
                        className="px-6 py-3 rounded-full font-bold bg-white text-[#3B5C3A] text-center shadow-md hover:bg-[#F5F0E8] transition-all cursor-pointer w-full max-w-[240px]"
                      >
                        Enquire to Volunteer
                      </button>
                      <button 
                        onClick={() => {
                          const contactBlock = document.getElementById('contact-form-section');
                          contactBlock?.scrollIntoView({ behavior: 'smooth' });
                        }}
                        className="px-6 py-3 rounded-full font-semibold border border-white/30 text-white hover:bg-white/10 transition-all text-center w-full max-w-[240px]"
                      >
                        Contact Us Today
                      </button>
                    </div>
                  </div>
                </div>
              </section>

              {/* Newsletter & Physical Location Contact Block */}
              <section id="contact-form-section" className="py-20 bg-[#F5F0E8] border-b border-[#C4B89A]">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                  <div className="grid grid-1 md:grid-cols-2 gap-12">
                    
                    {/* Left: Newsletter Sign Up Box */}
                    <div className="bg-white rounded-2xl border border-[#C4B89A] p-8 shadow-sm flex flex-col justify-between">
                      <div>
                        <div className="w-12 h-12 rounded-full bg-[#3B5C3A]/10 flex items-center justify-center text-[#3B5C3A] mb-4 text-xl">
                          ✉️
                        </div>
                        <h4 className="font-display text-2xl text-[#3B5C3A] mb-2">Stay Connected in the Hills</h4>
                        <p className="text-gray-600 text-sm leading-relaxed mb-6">
                          Subscribe to our community newsletter and receive monthly café menu releases, workshop calendars, local Wassail dates, and volunteer alerts.
                        </p>
                      </div>

                      {newsletterSubscribed ? (
                        <div className="bg-[#6B8F5E]/10 border border-[#6B8F5E]/30 p-6 rounded-xl text-center">
                          <Check className="w-8 h-8 text-[#3B5C3A] mx-auto mb-2" />
                          <p className="font-bold text-[#3B5C3A]">You are subscribed!</p>
                          <p className="text-xs text-gray-500 mt-1">Under WordPress, this hooks directly into your Mailchimp audience integration list.</p>
                        </div>
                      ) : (
                        <form onSubmit={(e) => { e.preventDefault(); setNewsletterSubscribed(true); }} className="space-y-3">
                          <div>
                            <label className="sr-only">Email Address</label>
                            <input 
                              type="email" 
                              required
                              placeholder="Enter your email address..." 
                              value={newsletterEmail}
                              onChange={(e) => setNewsletterEmail(e.target.value)}
                              className="w-full px-4 py-3 rounded-full border border-[#C4B89A] bg-[#F5F0E8]/20 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B5C3A]"
                            />
                          </div>
                          <button type="submit" className="w-full px-5 py-3 rounded-full font-bold bg-[#3B5C3A] text-white text-sm hover:bg-[#6B8F5E] transition-all cursor-pointer">
                            Sign Up for Newsletter
                          </button>
                        </form>
                      )}
                    </div>

                    {/* Right: Get In Touch Enquiry Box */}
                    <div className="bg-white rounded-2xl border border-[#C4B89A] p-8 shadow-sm">
                      <div className="flex items-center gap-3 mb-6">
                        <MapPin className="w-6 h-6 text-[#3B5C3A]" />
                        <h4 className="font-display text-2xl text-[#3B5C3A]">Get in Touch</h4>
                      </div>

                      {contactSubmitted ? (
                        <div className="bg-[#6B8F5E]/10 border border-[#6B8F5E]/30 p-6 rounded-xl text-center">
                          <Send className="w-8 h-8 text-[#3B5C3A] mx-auto mb-2" />
                          <p className="font-bold text-[#3B5C3A]">Message sent with care!</p>
                          <p className="text-xs text-gray-500 mt-1">In production, this maps to standard Contact Form 7 endpoints.</p>
                        </div>
                      ) : (
                        <form onSubmit={(e) => { e.preventDefault(); setContactSubmitted(true); }} className="space-y-4">
                          <div className="grid grid-cols-2 gap-4">
                            <div>
                              <label className="block text-xs font-bold text-gray-700 mb-1">Your Name</label>
                              <input 
                                type="text" 
                                required
                                value={contactForm.name}
                                onChange={(e) => setContactForm({ ...contactForm, name: e.target.value })}
                                className="w-full px-3 py-2 rounded border border-[#C4B89A] text-sm focus:outline-none focus:ring-1 focus:ring-[#3B5C3A]"
                              />
                            </div>
                            <div>
                              <label className="block text-xs font-bold text-gray-700 mb-1">Email Address</label>
                              <input 
                                type="email" 
                                required
                                value={contactForm.email}
                                onChange={(e) => setContactForm({ ...contactForm, email: e.target.value })}
                                className="w-full px-3 py-2 rounded border border-[#C4B89A] text-sm focus:outline-none focus:ring-1 focus:ring-[#3B5C3A]"
                              />
                            </div>
                          </div>
                          <div>
                            <label className="block text-xs font-bold text-gray-700 mb-1">Your Message</label>
                            <textarea 
                              required
                              rows={3}
                              value={contactForm.message}
                              onChange={(e) => setContactForm({ ...contactForm, message: e.target.value })}
                              className="w-full px-3 py-2 rounded border border-[#C4B89A] text-sm focus:outline-none focus:ring-1 focus:ring-[#3B5C3A]"
                            />
                          </div>
                          <button type="submit" className="w-full px-5 py-2.5 rounded-full font-bold bg-[#3B5C3A] text-white text-sm hover:bg-[#6B8F5E] transition-all cursor-pointer shadow-sm">
                            Send Message
                          </button>
                        </form>
                      )}
                    </div>

                  </div>
                </div>
              </section>

              {/* Dynamic Volunteer Form Block */}
              <section id="volunteer-form-section" className="py-20 bg-white border-b border-[#C4B89A]">
                <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                  <div className="bg-white border border-[#C4B89A] rounded-2xl p-8 shadow-sm">
                    <div className="text-center mb-8">
                      <Heart className="w-10 h-10 text-[#3B5C3A] mx-auto mb-2" />
                      <h4 className="font-display text-2xl text-[#3B5C3A]">Volunteer Application Form</h4>
                      <p className="text-sm text-gray-500 mt-1">Submit your details to volunteer with our local Community Interest Company strands.</p>
                    </div>

                    {volunteerSubmitted ? (
                      <div className="bg-[#6B8F5E]/15 border border-[#6B8F5E]/30 p-8 rounded-xl text-center">
                        <Check className="w-10 h-10 text-[#3B5C3A] mx-auto mb-3" />
                        <h5 className="font-display text-xl text-[#3B5C3A] mb-1">Application Submitted</h5>
                        <p className="text-sm text-gray-600">Thank you for stepping forward! We will review your application and Cheryl or Hollie will call you soon.</p>
                      </div>
                    ) : (
                      <form onSubmit={(e) => { e.preventDefault(); setVolunteerSubmitted(true); }} className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                          <div>
                            <label className="block text-xs font-bold text-gray-700 mb-1">Full Name</label>
                            <input 
                              type="text" 
                              required
                              value={volunteerForm.name}
                              onChange={(e) => setVolunteerForm({ ...volunteerForm, name: e.target.value })}
                              className="w-full px-3 py-2 rounded border border-[#C4B89A] text-sm focus:ring-1 focus:ring-[#3B5C3A]"
                            />
                          </div>
                          <div>
                            <label className="block text-xs font-bold text-gray-700 mb-1">Email Address</label>
                            <input 
                              type="email" 
                              required
                              value={volunteerForm.email}
                              onChange={(e) => setVolunteerForm({ ...volunteerForm, email: e.target.value })}
                              className="w-full px-3 py-2 rounded border border-[#C4B89A] text-sm focus:ring-1 focus:ring-[#3B5C3A]"
                            />
                          </div>
                        </div>
                        <div>
                          <label className="block text-xs font-bold text-gray-700 mb-1">Which strand are you interested in?</label>
                          <select 
                            value={volunteerForm.role}
                            onChange={(e) => setVolunteerForm({ ...volunteerForm, role: e.target.value })}
                            className="w-full px-3 py-2 rounded border border-[#C4B89A] text-sm bg-white focus:ring-1 focus:ring-[#3B5C3A]"
                          >
                            <option value="kitchen">Root & Branch Café Help</option>
                            <option value="creative">Creative Roots Workshop Teaching</option>
                            <option value="youth">Youth Programme Forest School Guide</option>
                            <option value="roots-shoots">Roots & Shoots Baby Playroom Support</option>
                            <option value="driver">Community Driver / General Help</option>
                          </select>
                        </div>
                        <div>
                          <label className="block text-xs font-bold text-gray-700 mb-1">Tell us a bit about yourself</label>
                          <textarea 
                            rows={3}
                            placeholder="e.g. your hobbies, why you want to support Weardale, or any relevant experience..."
                            value={volunteerForm.notes}
                            onChange={(e) => setVolunteerForm({ ...volunteerForm, notes: e.target.value })}
                            className="w-full px-3 py-2 rounded border border-[#C4B89A] text-sm focus:ring-1 focus:ring-[#3B5C3A]"
                          />
                        </div>
                        <button type="submit" className="w-full px-5 py-3 rounded-full font-bold bg-[#3B5C3A] text-white text-sm hover:bg-[#6B8F5E] transition-all cursor-pointer">
                          Submit Application
                        </button>
                      </form>
                    )}
                  </div>
                </div>
              </section>

            </motion.div>
          )}

          {/* ========================================================= */}
          {/* 3. ROOT & BRANCH CAFE ROOM (Sandy/Cosy/Food)               */}
          {/* ========================================================= */}
          {activeTab === 'cafe' && (
            <motion.div
              key="cafe"
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              className="py-16 bg-[#F5F0E8]"
            >
              <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                {/* Header Card */}
                <div className="bg-white border-2 border-[#C4956A] rounded-2xl p-8 shadow-sm mb-8 text-center relative overflow-hidden">
                  <div className="absolute top-0 left-0 w-full h-2 bg-[#C4956A]"></div>
                  <span className="text-3xl block mb-2">☕</span>
                  <h2 className="font-display text-3xl text-[#9E6B3E] mb-2">Root & Branch Café</h2>
                  <p className="text-gray-500 font-sans italic">"A warm, no-obligation space with food made with genuine care"</p>
                </div>

                {/* Content columns */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                  <div className="bg-white border border-[#C4B89A] rounded-2xl p-6 shadow-sm">
                    <h3 className="font-display text-xl text-[#9E6B3E] mb-4">Cosy Kitchen & Sourdough</h3>
                    <p className="text-sm text-gray-600 leading-relaxed mb-4">
                      The Root & Branch Café is the physical and social heartbeat of Weardale Together. Our warm sandy interior, smells of toasted local grains, and freshly brewed coffee greet everyone with a friendly smile.
                    </p>
                    <p className="text-sm text-gray-600 leading-relaxed">
                      We believe everyone deserves fresh, home-cooked food made with real care. That is why our breads undergo slow 24-hour fermentation, and our soups change daily depending on which ingredients our local Pennine partners supply.
                    </p>
                  </div>

                  {/* Polaroid layout food photography */}
                  <div className="flex flex-col items-center justify-center bg-white border border-[#C4B89A] rounded-2xl p-6 shadow-sm">
                    <div className="bg-white p-3 pb-8 shadow-md border border-gray-100 rotate-[-2deg] hover:rotate-0 transition-all max-w-[260px]">
                      <div className="w-[230px] h-[180px] bg-[#E8A020]/15 rounded flex items-center justify-center text-gray-400 font-mono text-xs">
                        [ Polaroid: Hot Bread ]
                      </div>
                      <p className="text-xs text-center font-mono mt-3 text-gray-500 italic">"Slow Sourdough fermenting" &bull; Cheryl</p>
                    </div>
                  </div>
                </div>

                {/* Opening Hours list */}
                <div className="bg-white border border-[#C4B89A] rounded-2xl p-8 shadow-sm">
                  <h3 className="font-display text-xl text-[#9E6B3E] mb-6 flex items-center gap-2">
                    <Clock className="w-5 h-5 text-[#C4956A]" />
                    Hours & Locations
                  </h3>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm text-gray-600">
                    <div>
                      <h4 className="font-bold text-gray-800 mb-2">Stanhope Central Hub</h4>
                      <p>12 High Street, Stanhope, County Durham, DL13</p>
                      <p className="mt-2 font-mono text-xs text-gray-500">Opposite Community Garden</p>
                    </div>
                    <div>
                      <ul className="space-y-2">
                        <li className="flex justify-between border-b border-[#F5F0E8] pb-1"><span>Tuesday - Thursday</span> <strong>10:00 AM - 4:00 PM</strong></li>
                        <li className="flex justify-between border-b border-[#F5F0E8] pb-1"><span>Friday - Saturday</span> <strong>10:00 AM - 6:00 PM</strong></li>
                        <li className="flex justify-between text-red-500"><span>Sunday - Monday</span> <strong>Closed</strong></li>
                      </ul>
                    </div>
                  </div>
                </div>

                {/* Related News & Stories */}
                <div className="mt-12 pt-12 border-t border-[#C4B89A]/40">
                  <h3 className="font-display text-2xl text-[#9E6B3E] mb-6 flex items-center gap-2">
                    <span>📰</span> News & Updates from Root & Branch Café
                  </h3>
                  {posts.filter(p => p.strand === 'cafe').length === 0 ? (
                    <p className="text-sm text-gray-500 italic">No news stories are currently associated with this strand.</p>
                  ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      {posts.filter(p => p.strand === 'cafe').map(p => (
                        <div key={p.id} className="bg-white border border-[#C4B89A] rounded-xl p-6 shadow-sm flex flex-col justify-between">
                          <div>
                            <div className="text-[10px] font-mono text-gray-400 mb-1">{p.date} &bull; by {p.author}</div>
                            <h4 className="font-display font-semibold text-[#9E6B3E] text-base mb-2">{p.title}</h4>
                            <p className="text-xs text-gray-600 line-clamp-3 mb-4">{p.excerpt}</p>
                          </div>
                          <button 
                            onClick={() => alert(`Story details:\n\n${p.content}`)}
                            className="text-xs font-bold text-[#9E6B3E] hover:underline self-start"
                          >
                            Read full update &rarr;
                          </button>
                        </div>
                      ))}
                    </div>
                  )}
                </div>

              </div>
            </motion.div>
          )}

          {/* ========================================================= */}
          {/* 4. CREATIVE ARTS ROOM (Botanical/Earthy)                   */}
          {/* ========================================================= */}
          {activeTab === 'creative' && (
            <motion.div
              key="creative"
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              className="py-16 bg-[#F5F0E8]"
            >
              <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div className="bg-white border-2 border-[#E8A020] rounded-2xl p-8 shadow-sm mb-8 text-center relative overflow-hidden">
                  <div className="absolute top-0 left-0 w-full h-2 bg-[#E8A020]"></div>
                  <span className="text-3xl block mb-2">🎨</span>
                  <h2 className="font-display text-3xl text-[#BA7D0C] mb-2">Creative Arts / Creative Roots</h2>
                  <p className="text-gray-500 font-sans italic">"For people who don’t think of themselves as creative"</p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                  <div className="bg-white border border-[#C4B89A] rounded-2xl p-6 shadow-sm">
                    <h3 className="font-display text-xl text-[#BA7D0C] mb-4">Botanical Crafts & Community</h3>
                    <p className="text-sm text-gray-600 leading-relaxed mb-4">
                      Our Creative Arts program, anchored by our seasonal **Creative Roots** project, celebrates the natural botanical life of the North Pennines. We use natural wood, wild wool, and plant pigments to tell our rural histories.
                    </p>
                    <p className="text-sm text-gray-600 leading-relaxed">
                      If you have never picked up a paintbrush or woodcarving knife before, this is the perfect room for you. We provide gentle, step-by-step guidance to ensure anyone can explore clay moulding, linocut printing, or sketching.
                    </p>
                  </div>

                  {/* Botanical Ink Drawing Illustration Representation */}
                  <div className="bg-white border-2 border-dashed border-[#C4B89A] rounded-2xl p-8 flex flex-col items-center justify-center text-center shadow-sm">
                    <div className="w-32 h-32 border-2 border-black rounded-full flex items-center justify-center text-3xl font-bold bg-white relative">
                      🍃
                      <div className="absolute -bottom-2 -right-2 text-2xl">☀️</div>
                    </div>
                    <h4 className="font-display text-gray-800 mt-4 text-sm font-semibold">"Sun, Soil & Roots" Ink Motif</h4>
                    <p className="text-xs text-gray-500 mt-1 max-w-[200px]">Our consistent botanical visual anchor representing growth in Weardale.</p>
                  </div>
                </div>

                {/* Seasonal wash description */}
                <div className="bg-[#E8A020]/5 border border-[#E8A020]/20 rounded-2xl p-8 text-center">
                  <h4 className="font-display text-lg text-[#BA7D0C] mb-2">Seasonal Golden Amber Wash</h4>
                  <p className="text-sm text-gray-600 leading-relaxed max-w-xl mx-auto">
                    To capture the cycles of rural Pennine life, our page layouts, background gradients, and accents shift slowly over the year: **Golden Amber** for hot summer harvests, **Deep Clay Terracotta** for winter Wassails, and **Sage Green** for spring budding.
                  </p>
                </div>

                {/* Related News & Stories */}
                <div className="mt-12 pt-12 border-t border-[#C4B89A]/40">
                  <h3 className="font-display text-2xl text-[#BA7D0C] mb-6 flex items-center gap-2">
                    <span>📰</span> News & Stories from Creative Arts
                  </h3>
                  {posts.filter(p => p.strand === 'creative').length === 0 ? (
                    <p className="text-sm text-gray-500 italic">No news stories are currently associated with this strand.</p>
                  ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      {posts.filter(p => p.strand === 'creative').map(p => (
                        <div key={p.id} className="bg-white border border-[#C4B89A] rounded-xl p-6 shadow-sm flex flex-col justify-between">
                          <div>
                            <div className="text-[10px] font-mono text-gray-400 mb-1">{p.date} &bull; by {p.author}</div>
                            <h4 className="font-display font-semibold text-[#BA7D0C] text-base mb-2">{p.title}</h4>
                            <p className="text-xs text-gray-600 line-clamp-3 mb-4">{p.excerpt}</p>
                          </div>
                          <button 
                            onClick={() => alert(`Story details:\n\n${p.content}`)}
                            className="text-xs font-bold text-[#BA7D0C] hover:underline self-start"
                          >
                            Read full update &rarr;
                          </button>
                        </div>
                      ))}
                    </div>
                  )}
                </div>

              </div>
            </motion.div>
          )}

          {/* ========================================================= */}
          {/* 5. YOUNG PEOPLE ROOM (Bold/Collage/Youth)                 */}
          {/* ========================================================= */}
          {activeTab === 'youth' && (
            <motion.div
              key="youth"
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              className="py-16 bg-[#F5F0E8] strand-youth"
            >
              <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                {/* Torn paper collage header card */}
                <div className="bg-white border-4 border-black shadow-[6px_6px_0_#2C2C2A] rounded-none p-8 mb-12 text-center relative">
                  <span className="text-4xl block mb-2">🌲</span>
                  <h2 className="font-display text-3xl text-[#E8962A] uppercase font-black tracking-tight mb-2">Young People & Forest School</h2>
                  <p className="text-gray-700 font-sans font-bold uppercase tracking-wider text-xs">Deliberately Different. Bold energy. Your room.</p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                  <div className="bg-white border-3 border-black p-6 shadow-[4px_4px_0_#2C2C2A]">
                    <h3 className="font-display text-lg text-[#E8962A] uppercase font-bold mb-4">Adventure & Outdoor Camps</h3>
                    <p className="text-sm text-gray-600 leading-relaxed mb-4">
                      Welcome to the loud, bold room of the house! Our Youth Club and Forest School sub-programs are led with high-contrast collage designs and high-energy orange washes, ensuring youth feel completely at home in their own space.
                    </p>
                    <p className="text-sm text-gray-600 leading-relaxed">
                      We organize weekly outdoor bushcraft projects, muddy kitchens, shelter-making camps, wood carving safely, campfire cooking, and team games. We expect dirty boots and loud voices here!
                    </p>
                  </div>

                  {/* Collage-style block card */}
                  <div className="bg-[#E8962A] text-white p-8 border-4 border-black shadow-[6px_6px_0_#2C2C2A] flex flex-col justify-between">
                    <div>
                      <h4 className="font-display text-lg font-bold uppercase mb-2">Forest School Sub-Page</h4>
                      <p className="text-sm leading-relaxed text-zinc-100 mb-6">
                        In WordPress, our sub-pages like /young-people/forest-school/ inherit specific high-impact CSS tags to preserve our separate, rebellious collage atmosphere.
                      </p>
                    </div>
                    <button 
                      onClick={() => alert('Forest School template active!')}
                      className="px-4 py-2 font-black uppercase text-black bg-white border-3 border-black text-xs hover:bg-zinc-100 transition-all self-start"
                    >
                      Visit Forest School
                    </button>
                  </div>
                </div>

                {/* Related News & Stories */}
                <div className="mt-12 pt-12 border-t border-black/15">
                  <h3 className="font-display text-2xl text-[#E8962A] uppercase font-bold mb-6 flex items-center gap-2">
                    <span>📰</span> Youth Club & Forest School Updates
                  </h3>
                  {posts.filter(p => p.strand === 'youth').length === 0 ? (
                    <p className="text-sm text-gray-500 italic">No news stories are currently associated with this strand.</p>
                  ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      {posts.filter(p => p.strand === 'youth').map(p => (
                        <div key={p.id} className="bg-white border-2 border-black p-6 shadow-[3px_3px_0_#2C2C2A] flex flex-col justify-between">
                          <div>
                            <div className="text-[10px] font-mono text-gray-500 mb-1">{p.date} &bull; by {p.author}</div>
                            <h4 className="font-display font-bold text-[#E8962A] text-base mb-2">{p.title}</h4>
                            <p className="text-xs text-gray-600 line-clamp-3 mb-4">{p.excerpt}</p>
                          </div>
                          <button 
                            onClick={() => alert(`Story details:\n\n${p.content}`)}
                            className="text-xs font-bold text-black hover:underline self-start uppercase tracking-wider"
                          >
                            Read update &rarr;
                          </button>
                        </div>
                      ))}
                    </div>
                  )}
                </div>

              </div>
            </motion.div>
          )}

          {/* ========================================================= */}
          {/* 6. ROOTS & SHOOTS ROOM (Soft/Terracotta/Child)           */}
          {/* ========================================================= */}
          {activeTab === 'roots-shoots' && (
            <motion.div
              key="roots-shoots"
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              className="py-16 bg-[#F5F0E8]"
            >
              <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div className="bg-white border border-[#C4B89A] rounded-3xl p-8 shadow-sm mb-8 text-center relative overflow-hidden">
                  <div className="absolute bottom-0 left-0 w-full h-2 bg-[#D4826A]"></div>
                  <span className="text-3xl block mb-2">🧸</span>
                  <h2 className="font-display text-3xl text-[#B2583E] mb-2">Roots & Shoots</h2>
                  <p className="text-gray-500 font-sans italic">"Soft, gentle playrooms for families and little sprouts"</p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div className="bg-white border border-[#C4B89A] rounded-3xl p-8 shadow-sm relative overflow-hidden">
                    <h3 className="font-display text-xl text-[#B2583E] mb-4">Unhurried Playroom</h3>
                    <p className="text-sm text-gray-600 leading-relaxed mb-4">
                      Roots & Shoots is our dedicated early-years playroom designed with soft terracotta illustrations, organic clays, baby pinks, and calming sage greens.
                    </p>
                    <p className="text-sm text-gray-600 leading-relaxed">
                      We offer a quiet, unhurried space for babies, toddlers, and their parent-carers. Step inside to share morning support discussions, sensory games, sand play, and soft music. No rush, no noise, just gentle growing.
                    </p>
                  </div>

                  <div className="bg-white border border-[#C4B89A] rounded-3xl p-8 shadow-sm flex flex-col justify-between">
                    <div>
                      <h4 className="font-display text-lg text-[#B2583E] mb-3">Playroom Sessions</h4>
                      <p className="text-sm text-gray-600 leading-relaxed mb-4">
                        Sessions are hosted every Monday and Thursday morning in Stanhope. Warm milk, tea for parents, and organic baby biscuits are provided with our compliments.
                      </p>
                    </div>
                    <div className="p-3 bg-[#D4826A]/5 rounded-xl border border-[#D4826A]/10 text-center font-mono text-[11px] text-gray-500">
                      Standard WCAG 2.2 safe-contrast colors
                    </div>
                  </div>
                </div>

                {/* Related News & Stories */}
                <div className="mt-12 pt-12 border-t border-[#C4B89A]/40">
                  <h3 className="font-display text-2xl text-[#B2583E] mb-6 flex items-center gap-2">
                    <span>📰</span> Roots & Shoots Family Updates
                  </h3>
                  {posts.filter(p => p.strand === 'roots-shoots').length === 0 ? (
                    <p className="text-sm text-gray-500 italic">No news stories are currently associated with this strand.</p>
                  ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      {posts.filter(p => p.strand === 'roots-shoots').map(p => (
                        <div key={p.id} className="bg-white border border-[#C4B89A] rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                          <div>
                            <div className="text-[10px] font-mono text-gray-400 mb-1">{p.date} &bull; by {p.author}</div>
                            <h4 className="font-display font-semibold text-[#B2583E] text-base mb-2">{p.title}</h4>
                            <p className="text-xs text-gray-600 line-clamp-3 mb-4">{p.excerpt}</p>
                          </div>
                          <button 
                            onClick={() => alert(`Story details:\n\n${p.content}`)}
                            className="text-xs font-bold text-[#B2583E] hover:underline self-start"
                          >
                            Read full update &rarr;
                          </button>
                        </div>
                      ))}
                    </div>
                  )}
                </div>

              </div>
            </motion.div>
          )}

          {/* ========================================================= */}
          {/* 7. WORDPRESS DEV CONSOLE (THE ULTIMATE FILE REVIEWER)    */}
          {/* ========================================================= */}
          {activeTab === 'console' && (
            <motion.div
              key="console"
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              className="py-12 bg-zinc-900 text-zinc-100 min-h-[700px] border-b border-[#C4B89A]"
            >
              <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                {/* Console header */}
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-zinc-800 pb-6 mb-8 gap-4">
                  <div>
                    <div className="flex items-center gap-2 mb-2">
                      <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                      <span className="text-xs font-mono uppercase tracking-widest text-emerald-400">Active WordPress Development Workspace</span>
                    </div>
                    <h2 className="font-display text-3xl text-zinc-100 flex items-center gap-2.5">
                      <Database className="w-8 h-8 text-emerald-400" />
                      WordPress Rebuild & File Explorer
                    </h2>
                    <p className="text-sm text-zinc-400 mt-1 font-sans">
                      Inspect the exact, real-code PHP theme templates and Custom Platform plugin files generated for the WT local XAMPP setup.
                    </p>
                  </div>
                  <div className="flex gap-2">
                    <button 
                      onClick={() => {
                        alert('XAMPP Guide file loaded below. Copy the setup steps to run locally!');
                        const doc = wordpressFiles.find(f => f.name === 'seed-db.sql') || wordpressFiles[0];
                        setSelectedFile(doc);
                      }}
                      className="px-4 py-2 rounded-lg bg-zinc-800 border border-zinc-700 text-sm hover:bg-zinc-700 transition-all font-mono font-semibold"
                    >
                      Database Seeding SQL
                    </button>
                  </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
                  
                  {/* Left sidebar: File Navigator */}
                  <div className="bg-zinc-950 p-5 rounded-xl border border-zinc-800 flex flex-col h-full min-h-[450px]">
                    <h3 className="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                      <FileCode className="w-4 h-4 text-emerald-500" />
                      Workspace Files
                    </h3>
                    
                    <div className="space-y-6 flex-grow overflow-y-auto">
                      
                      {/* Section: Themes */}
                      <div>
                        <span className="text-[10px] uppercase font-bold text-zinc-500 block mb-2 tracking-widest">Theme: weardale-together/</span>
                        <ul className="space-y-1 font-mono text-xs pl-2 border-l border-zinc-800">
                          {wordpressFiles.filter(f => f.type === 'theme').map(f => (
                            <li key={f.name}>
                              <button 
                                onClick={() => setSelectedFile(f)}
                                className={`w-full text-left py-1.5 px-2 rounded hover:bg-zinc-800 hover:text-emerald-400 transition-all ${selectedFile.name === f.name ? 'text-emerald-400 bg-zinc-900 font-bold' : 'text-zinc-400'}`}
                              >
                                {f.name}
                              </button>
                            </li>
                          ))}
                        </ul>
                      </div>

                      {/* Section: Plugins */}
                      <div>
                        <span className="text-[10px] uppercase font-bold text-zinc-500 block mb-2 tracking-widest">Plugin: weardale-platform/</span>
                        <ul className="space-y-1 font-mono text-xs pl-2 border-l border-zinc-800">
                          {wordpressFiles.filter(f => f.type === 'plugin').map(f => (
                            <li key={f.name}>
                              <button 
                                onClick={() => setSelectedFile(f)}
                                className={`w-full text-left py-1.5 px-2 rounded hover:bg-zinc-800 hover:text-emerald-400 transition-all ${selectedFile.name === f.name ? 'text-emerald-400 bg-zinc-900 font-bold' : 'text-zinc-400'}`}
                              >
                                {f.name === 'weardale-platform.php' ? 'weardale-platform.php' : 'includes/' + f.name}
                              </button>
                            </li>
                          ))}
                        </ul>
                      </div>

                      {/* Section: Scripts & Config */}
                      <div>
                        <span className="text-[10px] uppercase font-bold text-zinc-500 block mb-2 tracking-widest">Scripts / SQL Seeding</span>
                        <ul className="space-y-1 font-mono text-xs pl-2 border-l border-zinc-800">
                          {wordpressFiles.filter(f => f.type === 'script').map(f => (
                            <li key={f.name}>
                              <button 
                                onClick={() => setSelectedFile(f)}
                                className={`w-full text-left py-1.5 px-2 rounded hover:bg-zinc-800 hover:text-emerald-400 transition-all ${selectedFile.name === f.name ? 'text-emerald-400 bg-zinc-900 font-bold' : 'text-zinc-400'}`}
                              >
                                {f.name}
                              </button>
                            </li>
                          ))}
                        </ul>
                      </div>

                    </div>
                  </div>

                  {/* Right editor: Code viewer */}
                  <div className="lg:col-span-3 bg-zinc-950 rounded-xl border border-zinc-800 p-6 flex flex-col h-full">
                    
                    <div className="flex justify-between items-center border-b border-zinc-800 pb-4 mb-4 flex-wrap gap-3">
                      <div>
                        <span className="text-xs font-mono text-zinc-500 block">FILE PATH:</span>
                        <span className="text-sm font-mono text-emerald-400">{selectedFile.path}</span>
                      </div>
                      <button 
                        onClick={() => copyToClipboard(selectedFile.content)}
                        className={`px-4 py-2 rounded font-semibold text-xs flex items-center gap-1.5 transition-all cursor-pointer ${copiedFile ? 'bg-emerald-500 text-white' : 'bg-zinc-800 text-zinc-300 hover:bg-zinc-700'}`}
                      >
                        {copiedFile ? 'Copied to Clipboard!' : 'Copy File Content'}
                      </button>
                    </div>

                    <div className="flex-grow max-h-[500px] overflow-y-auto rounded bg-zinc-900 border border-zinc-850 p-4 font-mono text-xs leading-relaxed text-zinc-300 select-all">
                      <pre>{selectedFile.content}</pre>
                    </div>
                  </div>

                </div>

                {/* Local environment guides card */}
                <div className="mt-12 bg-zinc-950 p-8 rounded-xl border border-zinc-800">
                  <h3 className="font-display text-xl text-zinc-100 mb-4 flex items-center gap-2">
                    <HelpCircle className="w-5 h-5 text-emerald-400" />
                    How do I run this in my local WordPress setup?
                  </h3>
                  <p className="text-zinc-400 text-sm leading-relaxed mb-4">
                    The entire codebase has been developed to be completely portable without hardcoded file boundaries. To test and develop, simply map or clone this repository into your local XAMPP folder: <code className="bg-zinc-900 text-emerald-400 px-1.5 py-0.5 rounded">C:\xampp\htdocs\WT\</code>. Follow the step-by-step documentation we prepared for your team inside the `/docs/xampp-guide.md` file!
                  </p>
                  <div className="flex gap-4">
                    <a href="/docs/xampp-guide.md" target="_blank" className="text-sm font-bold text-emerald-400 hover:underline">&rarr; Read local XAMPP setup guide</a>
                    <a href="/docs/editor-guide.md" target="_blank" className="text-sm font-bold text-emerald-400 hover:underline">&rarr; Read content editor guide</a>
                  </div>
                </div>

              </div>
            </motion.div>
          )}

        </AnimatePresence>
      </main>

      {/* ========================================================= */}
      {/* 8. NEW EVENT INTERACTIVE CREATOR MODAL                    */}
      {/* ========================================================= */}
      {showEventModal && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
          <div className="bg-white rounded-2xl border border-[#C4B89A] max-w-lg w-full p-6 shadow-2xl overflow-y-auto max-h-[90vh]">
            <h4 className="font-display text-2xl text-[#3B5C3A] mb-2">Create Mock Event Listing</h4>
            <p className="text-xs text-gray-500 mb-6">Create a real activity strand event. This triggers a WordPress post metadata insertion simulation!</p>
            
            <form onSubmit={handleCreateEvent} className="space-y-4">
              <div>
                <label className="block text-xs font-bold text-gray-700 mb-1">Event Name</label>
                <input 
                  type="text" 
                  required
                  placeholder="e.g. Summer Clay Sculpting"
                  value={newEvent.title}
                  onChange={(e) => setNewEvent({ ...newEvent, title: e.target.value })}
                  className="w-full px-3 py-2 border border-[#C4B89A] rounded text-sm focus:outline-none focus:ring-1 focus:ring-[#3B5C3A]"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-gray-700 mb-1">Scheduled Date</label>
                  <input 
                    type="date" 
                    required
                    value={newEvent.date}
                    onChange={(e) => setNewEvent({ ...newEvent, date: e.target.value })}
                    className="w-full px-3 py-2 border border-[#C4B89A] rounded text-sm focus:outline-none focus:ring-1 focus:ring-[#3B5C3A]"
                  />
                </div>
                <div>
                  <label className="block text-xs font-bold text-gray-700 mb-1">Activity Strand</label>
                  <select 
                    value={newEvent.strand}
                    onChange={(e) => setNewEvent({ ...newEvent, strand: e.target.value as any })}
                    className="w-full px-3 py-2 border border-[#C4B89A] rounded text-sm bg-white focus:outline-none focus:ring-1 focus:ring-[#3B5C3A]"
                  >
                    <option value="cafe">Root & Branch Café</option>
                    <option value="creative">Creative Arts</option>
                    <option value="youth">Young People</option>
                    <option value="roots-shoots">Roots & Shoots</option>
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-gray-700 mb-1">Operating Hours</label>
                  <input 
                    type="text" 
                    required
                    placeholder="e.g. 10:00 AM - 1:00 PM"
                    value={newEvent.time}
                    onChange={(e) => setNewEvent({ ...newEvent, time: e.target.value })}
                    className="w-full px-3 py-2 border border-[#C4B89A] rounded text-sm"
                  />
                </div>
                <div>
                  <label className="block text-xs font-bold text-gray-700 mb-1">Admission Cost</label>
                  <input 
                    type="text" 
                    required
                    placeholder="e.g. Free (Booking Required)"
                    value={newEvent.cost}
                    onChange={(e) => setNewEvent({ ...newEvent, cost: e.target.value })}
                    className="w-full px-3 py-2 border border-[#C4B89A] rounded text-sm"
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 mb-1">Location Venue</label>
                <input 
                  type="text" 
                  required
                  placeholder="e.g. Stanhope Hub Community Gardens"
                  value={newEvent.location}
                  onChange={(e) => setNewEvent({ ...newEvent, location: e.target.value })}
                  className="w-full px-3 py-2 border border-[#C4B89A] rounded text-sm"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 mb-1">Event Description</label>
                <textarea 
                  required
                  rows={3}
                  placeholder="Write a warm, non-technical description..."
                  value={newEvent.content}
                  onChange={(e) => setNewEvent({ ...newEvent, content: e.target.value })}
                  className="w-full px-3 py-2 border border-[#C4B89A] rounded text-sm"
                />
              </div>

              <div className="flex gap-2 justify-end pt-4">
                <button 
                  type="button" 
                  onClick={() => setShowEventModal(false)}
                  className="px-4 py-2 rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 transition-all text-sm font-semibold"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  className="px-5 py-2 rounded-full bg-[#3B5C3A] text-white hover:bg-[#6B8F5E] transition-all text-sm font-bold shadow-md cursor-pointer"
                >
                  Create Listing
                </button>
              </div>

            </form>
          </div>
        </div>
      )}

      {/* 9. Footer copyright */}
      <footer className="bg-[#3B5C3A] text-[#F5F0E8] py-16 border-t border-[#C4B89A]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-12">
            <div>
              <h4 className="font-display text-2xl text-white mb-4">Weardale Together</h4>
              <p className="text-sm leading-relaxed text-[#F5F0E8]/90 mb-4">
                A grassroots Community Interest Company serving remote rural populations across Weardale in the North Pennines.
              </p>
              <p className="text-xs text-[#C4B89A]">
                CIC Number: 13483954 (Registered 2021)
              </p>
            </div>
            <div>
              <h5 className="font-semibold uppercase text-xs tracking-wider text-[#C4B89A] mb-4">Contact Info</h5>
              <p className="text-sm text-[#F5F0E8] mb-1"><strong>Address:</strong> Stanhope, County Durham, DL13</p>
              <p className="text-sm text-[#F5F0E8] mb-1"><strong>Email:</strong> hello@weardaletogether.org.uk</p>
              <p className="text-sm text-[#F5F0E8]"><strong>Phone:</strong> +44 (0) 1388 xxx xxx</p>
            </div>
            <div>
              <h5 className="font-semibold uppercase text-xs tracking-wider text-[#C4B89A] mb-4">Quick Navigation</h5>
              <ul className="text-sm space-y-2">
                <li><button onClick={() => { setActiveTab('home'); window.scrollTo(0,0); }} className="hover:underline text-left">Home Base</button></li>
                <li><button onClick={() => { setActiveTab('cafe'); window.scrollTo(0,0); }} className="hover:underline text-left">Root & Branch Café</button></li>
                <li><button onClick={() => { setActiveTab('creative'); window.scrollTo(0,0); }} className="hover:underline text-left">Creative Arts</button></li>
                <li><button onClick={() => { setActiveTab('youth'); window.scrollTo(0,0); }} className="hover:underline text-left">Young People</button></li>
                <li><button onClick={() => { setActiveTab('roots-shoots'); window.scrollTo(0,0); }} className="hover:underline text-left">Roots & Shoots</button></li>
              </ul>
            </div>
            <div>
              <h5 className="font-semibold uppercase text-xs tracking-wider text-[#C4B89A] mb-4">Newsletter Status</h5>
              <p className="text-sm leading-relaxed mb-4 text-[#F5F0E8]/80">
                Signup embeds are compatible with standard Mailchimp block configurations.
              </p>
              <div className="bg-white/10 p-3 rounded border border-white/10 text-center">
                <span className="text-[11px] text-[#C4B89A] italic">Embed widgets will populate form modules natively.</span>
              </div>
            </div>
          </div>
          
          <div className="border-t border-white/10 mt-12 pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-[#C4B89A] gap-4">
            <div>
              &copy; {new Date().getFullYear()} Weardale Together CIC. All rights reserved.
            </div>
            <div className="flex gap-4">
              <a href="#privacy" onClick={(e) => { e.preventDefault(); alert('Privacy Notice has been compiled into wordpress/wp-content/themes/weardale-together/page.php and is editable in WordPress Pages.'); }} className="hover:underline">Privacy Notice</a>
              <span className="text-white/20">|</span>
              <button onClick={() => setActiveTab('console')} className="text-emerald-400 hover:underline font-semibold font-mono flex items-center gap-1">
                <Code className="w-3.5 h-3.5" /> WP Dev Console
              </button>
            </div>
          </div>
        </div>
      </footer>

      {/* About WT Modal */}
      <AnimatePresence>
        {showAboutModal && (
          <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <motion.div 
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              className="bg-white rounded-2xl max-w-2xl w-full overflow-hidden shadow-2xl border border-[#C4B89A]"
            >
              <div className="bg-[#3B5C3A] text-[#F5F0E8] p-6">
                <h3 className="font-display text-2xl font-bold leading-none">About Weardale Together</h3>
                <p className="text-xs text-[#F5F0E8]/80 mt-2 uppercase tracking-wider font-semibold">Community Interest Company (CIC) • Stanhope</p>
              </div>
              <div className="p-6 md:p-8 space-y-4 max-h-[60vh] overflow-y-auto">
                <p className="text-base leading-relaxed text-gray-700">
                  Established in 2019, <strong>Weardale Together</strong> is a grassroots community initiative dedicated to ensuring that living in rural, isolated parts of the North Pennines doesn't mean living without access to creativity, warmth, and supportive networks.
                </p>
                
                <h4 className="font-display text-lg text-[#3B5C3A] font-semibold pt-2 border-b border-[#C4B89A]/40 pb-1">Our Core Mission</h4>
                <p className="text-sm leading-relaxed text-gray-600">
                  We operate a Hub-and-Spoke community model from our main Stanhope base, offering direct outreach and physical transport assistance to connecting villages including Wolsingham, St John’s Chapel, Rookhope, and Frosterley.
                </p>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3">
                  <div className="p-3 bg-[#F5F0E8]/40 border border-[#C4B89A]/30 rounded-lg">
                    <strong className="text-[#3B5C3A] block text-sm">☕ Root & Branch Café</strong>
                    <span className="text-xs text-gray-600">Volunteer-led community kitchen & food hub training space.</span>
                  </div>
                  <div className="p-3 bg-[#F5F0E8]/40 border border-[#C4B89A]/30 rounded-lg">
                    <strong className="text-[#3B5C3A] block text-sm">🎨 Creative Arts</strong>
                    <span className="text-xs text-gray-600">Handcrafted workshops, weaving, poetry, and storytelling.</span>
                  </div>
                  <div className="p-3 bg-[#F5F0E8]/40 border border-[#C4B89A]/30 rounded-lg">
                    <strong className="text-[#3B5C3A] block text-sm">🌲 Young People</strong>
                    <span className="text-xs text-gray-600">Weekly youth fellowships and Pennines trail exploration.</span>
                  </div>
                  <div className="p-3 bg-[#F5F0E8]/40 border border-[#C4B89A]/30 rounded-lg">
                    <strong className="text-[#3B5C3A] block text-sm">🧸 Roots & Shoots</strong>
                    <span className="text-xs text-gray-600">Safe, nature-integrated early play development for children.</span>
                  </div>
                </div>
              </div>
              <div className="bg-[#F5F0E8]/50 px-6 py-4 border-t border-[#C4B89A]/30 flex justify-end">
                <button 
                  onClick={() => setShowAboutModal(false)}
                  className="px-5 py-2 bg-[#3B5C3A] text-white rounded-lg hover:bg-[#6B8F5E] font-bold text-sm transition-all"
                >
                  Close & Explore
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>

      {/* Community Directory Modal */}
      <AnimatePresence>
        {showDirectoryModal && (
          <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <motion.div 
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              className="bg-white rounded-2xl max-w-3xl w-full overflow-hidden shadow-2xl border border-[#C4B89A]"
            >
              <div className="bg-[#3B5C3A] text-[#F5F0E8] p-6">
                <h3 className="font-display text-2xl font-bold leading-none">Weardale Community Directory</h3>
                <p className="text-xs text-[#F5F0E8]/80 mt-2 uppercase tracking-wider font-semibold">Active listings of local makers, venues, and support networks</p>
              </div>
              <div className="p-6 md:p-8 space-y-4 max-h-[60vh] overflow-y-auto">
                <p className="text-sm text-gray-600 leading-relaxed">
                  Below is an interactive view of verified listings that are dynamically linked inside our custom WordPress taxomony metadata system:
                </p>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {/* Listing 1 */}
                  <div className="p-4 bg-[#F5F0E8]/30 rounded-xl border border-[#C4B89A]/40 flex flex-col justify-between">
                    <div>
                      <span className="inline-block px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold uppercase tracking-wider mb-2">Food & Drink</span>
                      <h4 className="font-display font-bold text-base text-[#3B5C3A]">The Stanhope Artisan Bakery</h4>
                      <p className="text-xs text-gray-600 mt-2">Organic handcrafted sourdough, locally sourced Durham jams, and a warm workspace for remote rural workers.</p>
                    </div>
                    <div className="border-t border-[#C4B89A]/20 pt-3 mt-4 flex justify-between items-center text-[11px] text-gray-500 font-semibold">
                      <span>📍 Stanhope, DL13</span>
                      <span className="text-emerald-600">● Verified Active</span>
                    </div>
                  </div>

                  {/* Listing 2 */}
                  <div className="p-4 bg-[#F5F0E8]/30 rounded-xl border border-[#C4B89A]/40 flex flex-col justify-between">
                    <div>
                      <span className="inline-block px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-[10px] font-bold uppercase tracking-wider mb-2">Creative Arts</span>
                      <h4 className="font-display font-bold text-base text-[#3B5C3A]">Weardale Creative Makers Guild</h4>
                      <p className="text-xs text-gray-600 mt-2">Collective of weavers, potters, painters, and blacksmiths offering monthly local workshops and heritage exhibitions.</p>
                    </div>
                    <div className="border-t border-[#C4B89A]/20 pt-3 mt-4 flex justify-between items-center text-[11px] text-gray-500 font-semibold">
                      <span>📍 Wolsingham, DL13</span>
                      <span className="text-emerald-600">● Verified Active</span>
                    </div>
                  </div>

                  {/* Listing 3 */}
                  <div className="p-4 bg-[#F5F0E8]/30 rounded-xl border border-[#C4B89A]/40 flex flex-col justify-between">
                    <div>
                      <span className="inline-block px-2.5 py-0.5 rounded-full bg-orange-100 text-orange-800 text-[10px] font-bold uppercase tracking-wider mb-2">Young People</span>
                      <h4 className="font-display font-bold text-base text-[#3B5C3A]">Upper Weardale Youth Fellowship</h4>
                      <p className="text-xs text-gray-600 mt-2">Structured sports activities, outdoor trails, and mentorship for teenagers connecting rural Upper Weardale schools.</p>
                    </div>
                    <div className="border-t border-[#C4B89A]/20 pt-3 mt-4 flex justify-between items-center text-[11px] text-gray-500 font-semibold">
                      <span>📍 St John’s Chapel, DL13</span>
                      <span className="text-emerald-600">● Verified Active</span>
                    </div>
                  </div>

                  {/* Listing 4 */}
                  <div className="p-4 bg-[#F5F0E8]/30 rounded-xl border border-[#C4B89A]/40 flex flex-col justify-between">
                    <div>
                      <span className="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase tracking-wider mb-2">Community Cafe</span>
                      <h4 className="font-display font-bold text-base text-[#3B5C3A]">Root & Branch Community Cafe</h4>
                      <p className="text-xs text-gray-600 mt-2">Volunteer-led warm hub. Nutritious home-cooked meals served daily on a pay-as-you-can basis with dedicated training schemes.</p>
                    </div>
                    <div className="border-t border-[#C4B89A]/20 pt-3 mt-4 flex justify-between items-center text-[11px] text-gray-500 font-semibold">
                      <span>📍 Stanhope Hub, DL13</span>
                      <span className="text-emerald-600">● Verified Active</span>
                    </div>
                  </div>
                </div>
              </div>
              <div className="bg-[#F5F0E8]/50 px-6 py-4 border-t border-[#C4B89A]/30 flex justify-end">
                <button 
                  onClick={() => setShowDirectoryModal(false)}
                  className="px-5 py-2 bg-[#3B5C3A] text-white rounded-lg hover:bg-[#6B8F5E] font-bold text-sm transition-all"
                >
                  Close Directory
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>

    </div>
  );
}
