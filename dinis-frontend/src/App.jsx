import { useState } from 'react';
import Login from './components/Login';
import IdentityLookup from './components/IdentityLookup';
import RegisterCitizen from './components/RegisterCitizen';
// We import icons from lucide-react to give the sidebar a professional look
import { LogOut, ShieldCheck, Search, UserPlus, Network, Users, HeartHandshake, Globe, Printer } from 'lucide-react';
import FamilyTree from './components/FamilyTree';
import OrphanRegistry from './components/OrphanRegistry';
import MarriageRegistry from './components/MarriageRegistry';
import AlienRegistration from './components/AlienRegistration';
import PrintAndCapture from './components/PrintAndCapture';
import UserManagement from './components/UserManagement';


function App() {
    // ------------------------------------------------------------------------
    // 1. STATE MANAGEMENT & AUTHENTICATION
    // ------------------------------------------------------------------------
    
    // We check for the token immediately when the app loads.
    const [isAuthenticated, setIsAuthenticated] = useState(() => !!localStorage.getItem('token'));
    
    // This state tracks which module (tab) the user is currently viewing.
    const [activeTab, setActiveTab] = useState('lookup');
    
    // We retrieve the stored user data from localStorage.
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    // Handle user logout by clearing local storage and resetting authentication state
    const handleLogout = () => {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        setIsAuthenticated(false);
    };

    // ------------------------------------------------------------------------
    // 2. ROLE-BASED ACCESS CONTROL (RBAC) CONFIGURATION
    // ------------------------------------------------------------------------
    
    // This array defines every section of the application.
    // FIXED: Removed the duplicate "Print & Capture" item from this list!
    const navItems = [
        { id: 'lookup', label: 'Identity Lookup', icon: Search, roles: ['admin', 'officer'] },
        { id: 'register', label: 'Register NRC', icon: UserPlus, roles: ['admin', 'officer'] },
        { id: 'print', label: 'Print & Capture', icon: Printer, roles: ['admin', 'officer'] },
        { id: 'family', label: 'Family Trees', icon: Network, roles: ['admin', 'officer'] },
        { id: 'aliens', label: 'Alien/Refugee Registry', icon: Globe, roles: ['admin', 'officer'] },
        { id: 'marriage', label: 'Civil Marriages', icon: HeartHandshake, roles: ['admin', 'officer'] },
        { id: 'orphans', label: 'Orphan Registry', icon: Users, roles: ['admin', 'officer'] },
        // The 'System Users' module is strictly restricted to admins only.
        { id: 'users', label: 'System Users', icon: Users, roles: ['admin'] }, 
    ];

    // We filter the navigation items so the user only sees what their role permits.
    const visibleNavItems = navItems.filter(item => item.roles.includes(user.role));

    // ------------------------------------------------------------------------
    // 3. RENDER LOGIN OR DASHBOARD
    // ------------------------------------------------------------------------

    // If the user is not authenticated, show the Login screen and stop rendering here.
    if (!isAuthenticated) {
        return <Login onLoginSuccess={() => setIsAuthenticated(true)} />;
    }

    // If authenticated, render the main dashboard interface.
    return (
        <div className="min-h-screen bg-slate-50 font-sans flex">
            
            {/* --- SIDEBAR NAVIGATION --- */}
            <aside className="w-64 bg-slate-900 text-slate-300 flex flex-col shadow-2xl z-10 hidden md:flex">
                
                {/* System Branding / Logo Area */}
                <div className="p-6 bg-slate-950 border-b border-slate-800 flex items-center gap-3">
                    <div className="bg-blue-600 p-2 rounded-lg text-white">
                        <ShieldCheck size={24} />
                    </div>
                    <div>
                        <span className="text-xl font-black text-white tracking-tight block">DINIS</span>
                        <span className="text-xs text-slate-400 font-bold uppercase tracking-widest">{user.role} Console</span>
                    </div>
                </div>

                {/* Dynamic Menu Buttons */}
                <div className="p-4 flex-1 space-y-2">
                    <p className="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 px-2">Modules</p>
                    
                    {visibleNavItems.map((item) => {
                        const Icon = item.icon;
                        const isActive = activeTab === item.id;
                        
                        return (
                            <button
                                key={item.id}
                                onClick={() => setActiveTab(item.id)}
                                className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all ${
                                    isActive ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-slate-800 hover:text-white'
                                }`}
                            >
                                <Icon size={18} /> {item.label}
                            </button>
                        );
                    })}
                </div>

                {/* User Profile & Logout Area (Bottom of Sidebar) */}
                <div className="p-4 bg-slate-950 border-t border-slate-800">
                    <div className="flex items-center gap-3 px-2 mb-4">
                        <div className="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold uppercase">
                            {user.full_name?.charAt(0) || 'U'}
                        </div>
                        <div className="text-sm">
                            <p className="text-white font-bold">{user.full_name || 'System User'}</p>
                            <p className="text-slate-500 text-xs">{user.username}</p>
                        </div>
                    </div>
                    <button 
                        onClick={handleLogout} 
                        className="w-full flex items-center justify-center gap-2 text-sm font-bold text-red-400 hover:text-red-300 hover:bg-red-400/10 px-4 py-2 rounded-lg transition-all"
                    >
                        <LogOut size={16} /> Sign Out
                    </button>
                </div>
            </aside>

            {/* --- MAIN CONTENT AREA --- */}
            <main className="flex-1 p-8 overflow-y-auto">
                
                <header className="mb-8">
                    <h1 className="text-3xl font-black text-slate-800 tracking-tight">
                        {navItems.find(i => i.id === activeTab)?.label}
                    </h1>
                </header>

                <div className="animate-in fade-in slide-in-from-bottom-4 duration-500">
                    
                    {activeTab === 'lookup' && <IdentityLookup />}
                    {activeTab === 'register' && <RegisterCitizen />}
                    {activeTab === 'print' && <PrintAndCapture />}
                    {activeTab === 'family' && <FamilyTree />}
                    {activeTab === 'aliens' && <AlienRegistration />}
                    {activeTab === 'marriage' && <MarriageRegistry />}
                    {activeTab === 'orphans' && <OrphanRegistry />}
                    {activeTab === 'users' && <UserManagement />}
                </div>
            </main>
        </div>
    );
}

export default App;