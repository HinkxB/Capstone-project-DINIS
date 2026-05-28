import { useState } from 'react';
import api from '../api';
import { Lock, User, ShieldCheck, AlertCircle } from 'lucide-react';

const Login = ({ onLoginSuccess }) => {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setLoading(true);

        try {
            const response = await api.post('/login', { username, password });
            if (response.data?.token) {
                localStorage.setItem('token', response.data.token);
                // ADD THIS LINE to save the user data for Role-Based Access:
                localStorage.setItem('user', JSON.stringify(response.data.user)); 
                onLoginSuccess();
            }
        } catch (error) {
            console.error("Login Error:", error);
            setError('Authentication failed. Please verify credentials.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-slate-100 p-6">
            <div className="max-w-4xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row border border-slate-200">
                
                {/* Brand Side */}
                <div className="md:w-1/2 bg-blue-800 p-12 text-white flex flex-col justify-center items-center">
                    <ShieldCheck size={80} className="mb-6 text-blue-300" />
                    <h1 className="text-4xl font-black tracking-tighter">DINIS</h1>
                    <p className="text-blue-200 opacity-80 uppercase tracking-widest text-xs font-bold mt-2 text-center">
                        Digital National<br/>Identity System
                    </p>
                </div>

                {/* Form Side */}
                <div className="md:w-1/2 p-12 bg-white">
                    <h2 className="text-2xl font-bold text-slate-800 mb-2">Officer Login</h2>
                    <p className="text-slate-500 mb-8 text-sm">Secure biometric and identity verification access.</p>

                    <form onSubmit={handleSubmit} className="space-y-5">
                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-2">System ID</label>
                            <div className="relative">
                                <User className="absolute left-3 top-3.5 text-slate-400" size={18} />
                                <input 
                                    type="text" 
                                    className="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                    placeholder="Username"
                                    onChange={(e) => setUsername(e.target.value)}
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-2">Passcode</label>
                            <div className="relative">
                                <Lock className="absolute left-3 top-3.5 text-slate-400" size={18} />
                                <input 
                                    type="password" 
                                    className="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                    placeholder="••••••••"
                                    onChange={(e) => setPassword(e.target.value)}
                                    required
                                />
                            </div>
                        </div>

                        {error && (
                            <div className="flex items-center gap-2 bg-red-50 text-red-600 p-3 rounded-xl border border-red-100 text-sm font-semibold">
                                <AlertCircle size={16} /> {error}
                            </div>
                        )}

                        <button 
                            type="submit" 
                            disabled={loading}
                            className="w-full bg-blue-700 hover:bg-blue-800 text-white py-3 rounded-xl font-bold shadow-lg shadow-blue-200 transition-all active:scale-95 disabled:bg-slate-300 mt-4"
                        >
                            {loading ? 'Authenticating...' : 'Access Portal'}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default Login;