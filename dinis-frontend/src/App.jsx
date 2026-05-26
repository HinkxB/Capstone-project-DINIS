import { useState } from 'react';
import Login from './components/Login';
import IdentityLookup from './components/IdentityLookup';
import { LogOut, ShieldCheck } from 'lucide-react';

function App() {
    // 1. We check localStorage immediately using a function inside useState.
    // The "!!" turns the token string into a true/false boolean.
    // No useEffect needed!
    const [isAuthenticated, setIsAuthenticated] = useState(() => {
        return !!localStorage.getItem('token');
    });

    const handleLogout = () => {
        localStorage.removeItem('token');
        setIsAuthenticated(false);
    };

    return (
        <div className="min-h-screen bg-slate-50 font-sans">
            {!isAuthenticated ? (
                <Login onLoginSuccess={() => setIsAuthenticated(true)} />
            ) : (
                <div className="max-w-6xl mx-auto p-4 md:p-8">
                    
                    {/* Top Navigation Bar */}
                    <nav className="flex justify-between items-center mb-8 bg-white px-8 py-5 rounded-2xl shadow-sm border border-slate-200">
                        <div className="flex items-center gap-3">
                            <div className="bg-blue-700 p-2 rounded-lg text-white shadow-md">
                                <ShieldCheck size={20} />
                            </div>
                            <span className="text-xl font-black text-slate-800 tracking-tight">
                                DINIS <span className="text-blue-700 font-light italic">Console</span>
                            </span>
                        </div>
                        <button 
                            onClick={handleLogout} 
                            className="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-red-600 hover:bg-red-50 px-4 py-2 rounded-xl transition-all"
                        >
                            <LogOut size={18} /> Sign Out
                        </button>
                    </nav>

                    {/* Main Lookup Component */}
                    <IdentityLookup />
                </div>
            )}
        </div>
    );
}

export default App;