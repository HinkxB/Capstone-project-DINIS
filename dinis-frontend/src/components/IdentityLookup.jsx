import { useState } from 'react';
import { Search, User, MapPin, Calendar,  } from 'lucide-react';
import api from '../api';

const IdentityLookup = () => {
    const [searchNrc, setSearchNrc] = useState('');
    const [citizenData, setCitizenData] = useState(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState('');

    const handleSearch = async (e) => {
        e.preventDefault();
        if (!searchNrc) return;

        setIsLoading(true);
        setError('');
        setCitizenData(null);

        try {
            const response = await api.get(`/identity/${encodeURIComponent(searchNrc)}`);
            setCitizenData(response.data.data);
        } catch (err) {
            setError(err.response?.data?.message || 'Record not found.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div className="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <h2 className="text-2xl font-black text-slate-800 flex items-center gap-2 mb-6">
                <Search className="text-blue-600" /> Identity Lookup
            </h2>

            {/* Search Bar */}
            <form onSubmit={handleSearch} className="flex gap-4 mb-8">
                <input 
                    type="text" 
                    placeholder="Enter NRC (e.g. 123456/78/1)" 
                    value={searchNrc} 
                    onChange={(e) => setSearchNrc(e.target.value)}
                    className="flex-1 p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-lg font-mono"
                />
                <button 
                    type="submit" 
                    disabled={isLoading}
                    className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-xl transition-all disabled:opacity-50"
                >
                    {isLoading ? 'Searching...' : 'Lookup'}
                </button>
            </form>

            {/* Error Message */}
            {error && (
                <div className="bg-red-50 p-4 rounded-xl border border-red-200 text-red-700 font-medium mb-6">
                    {error}
                </div>
            )}

            {/* Results Card */}
            {citizenData && (
                <div className="bg-slate-50 border border-slate-200 rounded-xl p-6">
                    <div className="flex items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                        <div className={`w-16 h-16 rounded-full flex items-center justify-center text-white ${citizenData.sex === 'M' ? 'bg-blue-500' : 'bg-pink-500'}`}>
                            <User size={32} />
                        </div>
                        <div>
                            <h3 className="text-2xl font-black text-slate-800">{citizenData.maiden_full_name}</h3>
                            <p className="text-lg font-mono text-slate-500">{citizenData.nrc_number}</p>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div className="flex items-center gap-3">
                            <Calendar className="text-slate-400" />
                            <div>
                                <p className="text-xs font-bold text-slate-400 uppercase">Date of Birth</p>
                                <p className="font-medium text-slate-700">{citizenData.date_of_birth}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3">
                            <User className="text-slate-400" />
                            <div>
                                <p className="text-xs font-bold text-slate-400 uppercase">Sex</p>
                                <p className="font-medium text-slate-700">{citizenData.sex === 'M' ? 'Male' : 'Female'}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3">
                            <MapPin className="text-slate-400" />
                            <div>
                                <p className="text-xs font-bold text-slate-400 uppercase">Chiefdom</p>
                                <p className="font-medium text-slate-700">{citizenData.chiefdom_name}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3">
                            <MapPin className="text-slate-400" />
                            <div>
                                <p className="text-xs font-bold text-slate-400 uppercase">Village</p>
                                <p className="font-medium text-slate-700">{citizenData.village_name}</p>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default IdentityLookup;