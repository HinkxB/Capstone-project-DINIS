import { useState } from 'react';
import { Search, Printer, Fingerprint, Camera, AlertTriangle, CheckCircle } from 'lucide-react';
import api from '../api';

const PrintAndCapture = () => {
    const [searchNrc, setSearchNrc] = useState('');
    const [citizen, setCitizen] = useState(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState('');
    const [actionMessage, setActionMessage] = useState(null);

    const calculateAge = (dob) => {
        const birthDate = new Date(dob);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    };

    const handleSearch = async (e) => {
        e.preventDefault();
        if (!searchNrc) return;

        setIsLoading(true);
        setError('');
        setCitizen(null);
        setActionMessage(null);

        try {
            // Using the same endpoint as IdentityLookup
            const response = await api.get(`/identity/${encodeURIComponent(searchNrc)}`);
            setCitizen(response.data.data);
        } catch (err) {
            setError(err.response?.data?.message || 'Record not found.');
        } finally {
            setIsLoading(false);
        }
    };

    const handleAction = (action) => {
        // In a real app, this would trigger hardware (webcam, fingerprint scanner, printer)
        setActionMessage(`Initializing ${action} sequence for ${citizen.nrc_number}...`);
        setTimeout(() => {
            setActionMessage(`✅ ${action} completed successfully!`);
        }, 2000);
    };

    return (
        <div className="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <div className="mb-8 border-b border-slate-100 pb-6">
                <h2 className="text-2xl font-black text-slate-800 flex items-center gap-2">
                    <Printer className="text-slate-800" /> Print & Capture
                </h2>
                <p className="text-slate-500 mt-1">Capture biometrics and print physical ID cards for eligible citizens.</p>
            </div>

            {/* Search Bar */}
            <form onSubmit={handleSearch} className="flex gap-4 mb-8">
                <input 
                    type="text" 
                    placeholder="Enter NRC Number..." 
                    value={searchNrc} 
                    onChange={(e) => setSearchNrc(e.target.value)}
                    className="flex-1 p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-lg font-mono uppercase"
                />
                <button 
                    type="submit" 
                    disabled={isLoading}
                    className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-xl transition-all disabled:opacity-50 flex items-center gap-2"
                >
                    <Search size={20} /> {isLoading ? 'Searching...' : 'Find Record'}
                </button>
            </form>

            {error && (
                <div className="bg-red-50 p-4 rounded-xl border border-red-200 text-red-700 font-medium mb-6">
                    {error}
                </div>
            )}

            {actionMessage && (
                <div className="bg-green-50 p-4 rounded-xl border border-green-200 text-green-800 font-bold mb-6 text-center">
                    {actionMessage}
                </div>
            )}

            {citizen && (() => {
                const age = calculateAge(citizen.date_of_birth);
                const isEligible = age >= 16;

                return (
                    <div className="bg-slate-50 border border-slate-200 rounded-xl p-6">
                        <div className="flex justify-between items-start mb-6 pb-6 border-b border-slate-200">
                            <div>
                                <h3 className="text-2xl font-black text-slate-800">{citizen.maiden_full_name}</h3>
                                <p className="text-lg font-mono text-slate-500">{citizen.nrc_number}</p>
                                <p className="text-sm font-medium text-slate-600 mt-2">
                                    Date of Birth: {citizen.date_of_birth} <strong className="text-slate-800">(Age: {age})</strong>
                                </p>
                            </div>
                            
                            {/* Gatekeeper Status Badge */}
                            <div className={`px-4 py-2 rounded-lg font-bold flex items-center gap-2 ${isEligible ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'}`}>
                                {isEligible ? <CheckCircle size={20} /> : <AlertTriangle size={20} />}
                                {isEligible ? 'Eligible for Printing' : 'Underage - Capture Disabled'}
                            </div>
                        </div>

                        {/* Hardware Actions */}
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button 
                                onClick={() => handleAction('Photo Capture')}
                                disabled={!isEligible}
                                className={`p-6 rounded-xl border-2 flex flex-col items-center gap-3 transition-all ${isEligible ? 'border-slate-200 hover:border-blue-500 hover:bg-blue-50 text-slate-700' : 'border-slate-100 bg-slate-100 text-slate-400 cursor-not-allowed opacity-60'}`}
                            >
                                <Camera size={32} className={isEligible ? 'text-blue-600' : ''} />
                                <span className="font-bold">Take Photo</span>
                            </button>
                            
                            <button 
                                onClick={() => handleAction('Fingerprint Scan')}
                                disabled={!isEligible}
                                className={`p-6 rounded-xl border-2 flex flex-col items-center gap-3 transition-all ${isEligible ? 'border-slate-200 hover:border-blue-500 hover:bg-blue-50 text-slate-700' : 'border-slate-100 bg-slate-100 text-slate-400 cursor-not-allowed opacity-60'}`}
                            >
                                <Fingerprint size={32} className={isEligible ? 'text-blue-600' : ''} />
                                <span className="font-bold">Scan Fingerprints</span>
                            </button>

                            <button 
                                onClick={() => handleAction('ID Card Printing')}
                                disabled={!isEligible}
                                className={`p-6 rounded-xl border-2 flex flex-col items-center gap-3 transition-all ${isEligible ? 'border-slate-800 bg-slate-800 text-white hover:bg-slate-900' : 'border-slate-100 bg-slate-100 text-slate-400 cursor-not-allowed opacity-60'}`}
                            >
                                <Printer size={32} />
                                <span className="font-bold">Print NRC Card</span>
                            </button>
                        </div>
                        
                        {!isEligible && (
                            <p className="text-center text-amber-700 text-sm font-medium mt-6">
                                Zambian law requires citizens to be at least 16 years old to capture biometrics and receive a physical identity card.
                            </p>
                        )}
                    </div>
                );
            })()}
        </div>
    );
};

export default PrintAndCapture;