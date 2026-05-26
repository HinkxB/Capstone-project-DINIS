import { useState } from 'react';
import api from '../api';
import { Search, User as UserIcon, FileCheck } from 'lucide-react';

const IdentityLookup = () => {
    const [nrc, setNrc] = useState('');
    const [citizen, setCitizen] = useState(null);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSearch = async () => {
        if (!nrc) return;
        setLoading(true);
        setError('');
        try {
            const response = await api.get(`/citizens/${nrc}`);
            setCitizen(response.data?.data || response.data);
        } catch (error) {
            console.error("Lookup Error:", error);
            setError('NRC Registry: Record not found.');
            setCitizen(null);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="w-full max-w-5xl mx-auto space-y-6">
            
            {/* Search Bar */}
            <div className="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row gap-4">
                <div className="relative flex-1">
                    <Search className="absolute left-4 top-3.5 text-slate-400" size={20} />
                    <input 
                        className="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 font-medium"
                        placeholder="Search NRC (e.g., 123456/78/1)"
                        value={nrc}
                        onChange={(e) => setNrc(e.target.value)}
                        onKeyDown={(e) => e.key === 'Enter' && handleSearch()}
                    />
                </div>
                <button 
                    onClick={handleSearch}
                    disabled={loading}
                    className="bg-slate-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-black transition-all disabled:bg-slate-400 whitespace-nowrap"
                >
                    {loading ? 'Searching...' : 'Verify Identity'}
                </button>
            </div>

            {error && (
                <div className="bg-red-50 text-red-600 text-center font-semibold py-4 rounded-xl border border-red-100">
                    {error}
                </div>
            )}

            {/* Identity Card */}
            {citizen && citizen.identity_anchor && (
                <div className="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden animate-in fade-in duration-500">
                    <div className="bg-blue-800 p-8 text-white">
                        <div className="flex justify-between items-start">
                            <div>
                                <h3 className="text-3xl font-black uppercase tracking-tight">{citizen.identity_anchor.full_name}</h3>
                                <p className="text-blue-200 font-mono text-lg mt-1">
                                    NRC: {citizen.identity_anchor.nrc_number}
                                </p>
                            </div>
                            <div className="bg-white/10 p-4 rounded-2xl backdrop-blur-md">
                                <UserIcon size={40} />
                            </div>
                        </div>
                    </div>

                    <div className="p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div className="space-y-1">
                            <p className="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Gender</p>
                            <p className="text-lg font-bold text-slate-800">{citizen.identity_anchor.sex}</p>
                        </div>
                        <div className="space-y-1">
                            <p className="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Date of Birth</p>
                            <p className="text-lg font-bold text-slate-800">{citizen.identity_anchor.date_of_birth}</p>
                        </div>
                        <div className="space-y-1">
                            <p className="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Passport Status</p>
                            <div className="flex items-center gap-2">
                                <FileCheck className={citizen.linked_documents?.passport ? "text-green-500" : "text-slate-300"} size={18} />
                                <p className="font-bold text-slate-800">
                                    {citizen.linked_documents?.passport?.document_number || 'None Linked'}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default IdentityLookup;