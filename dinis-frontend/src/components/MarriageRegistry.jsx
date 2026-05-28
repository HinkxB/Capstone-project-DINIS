import { useState } from 'react';
import { HeartHandshake, HeartCrack, Save } from 'lucide-react';
import api from '../api';

const MarriageRegistry = () => {
    const [mode, setMode] = useState('marriage'); // 'marriage' or 'divorce'
    
    // Marriage Form State
    const [marriageData, setMarriageData] = useState({ husband_nrc: '', wife_nrc: '', date_of_marriage: '' });
    // Divorce Form State
    const [divorceData, setDivorceData] = useState({ certificate_number: '', date_of_divorce: '' });

    const [isLoading, setIsLoading] = useState(false);
    const [result, setResult] = useState(null);

    const handleMarriageSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);
        setResult(null);
        try {
            const response = await api.post('/marriages', marriageData);
            setResult({ type: 'success', message: response.data.message, extra: `Certificate: ${response.data.certificate_number}` });
            setMarriageData({ husband_nrc: '', wife_nrc: '', date_of_marriage: '' });
        } catch (err) {
            setResult({ type: 'error', message: err.response?.data?.message || 'Failed to register marriage.' });
        } finally {
            setIsLoading(false);
        }
    };

    const handleDivorceSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);
        setResult(null);
        try {
            const response = await api.post('/marriages/divorce', divorceData);
            setResult({ type: 'success', message: response.data.message });
            setDivorceData({ certificate_number: '', date_of_divorce: '' });
        } catch (err) {
            setResult({ type: 'error', message: err.response?.data?.message || 'Failed to register divorce.' });
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div className="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <div className="flex gap-4 mb-8 border-b border-slate-200 pb-4">
                <button 
                    onClick={() => { setMode('marriage'); setResult(null); }}
                    className={`flex items-center gap-2 px-6 py-3 rounded-lg font-bold transition-all ${mode === 'marriage' ? 'bg-pink-100 text-pink-700' : 'text-slate-500 hover:bg-slate-50'}`}
                >
                    <HeartHandshake size={20} /> Register Marriage
                </button>
                <button 
                    onClick={() => { setMode('divorce'); setResult(null); }}
                    className={`flex items-center gap-2 px-6 py-3 rounded-lg font-bold transition-all ${mode === 'divorce' ? 'bg-slate-800 text-white' : 'text-slate-500 hover:bg-slate-50'}`}
                >
                    <HeartCrack size={20} /> Process Divorce
                </button>
            </div>

            {result && result.type === 'success' && (
                <div className="mb-6 bg-green-50 p-4 rounded-xl border border-green-200 text-green-800 font-medium text-center">
                    {result.message} <br/>
                    {result.extra && <span className="font-mono font-black text-xl block mt-2">{result.extra}</span>}
                </div>
            )}

            {result && result.type === 'error' && (
                <div className="mb-6 bg-red-50 p-4 rounded-xl border border-red-200 text-red-700 font-medium">
                    {result.message}
                </div>
            )}

            {mode === 'marriage' ? (
                <form onSubmit={handleMarriageSubmit} className="space-y-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label className="block text-sm font-bold text-blue-700 mb-1">Husband's NRC</label>
                            <input type="text" required value={marriageData.husband_nrc} onChange={(e) => setMarriageData({...marriageData, husband_nrc: e.target.value})} placeholder="e.g. 123456/78/1" className="w-full p-3 bg-blue-50 border border-blue-200 rounded-xl focus:ring-2 focus:ring-pink-500 outline-none" />
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-pink-700 mb-1">Wife's NRC</label>
                            <input type="text" required value={marriageData.wife_nrc} onChange={(e) => setMarriageData({...marriageData, wife_nrc: e.target.value})} placeholder="e.g. 654321/78/1" className="w-full p-3 bg-pink-50 border border-pink-200 rounded-xl focus:ring-2 focus:ring-pink-500 outline-none" />
                        </div>
                    </div>
                    <div>
                        <label className="block text-sm font-bold text-slate-700 mb-1">Date of Marriage</label>
                        <input type="date" required value={marriageData.date_of_marriage} onChange={(e) => setMarriageData({...marriageData, date_of_marriage: e.target.value})} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-pink-500 outline-none" />
                    </div>
                    <button type="submit" disabled={isLoading} className="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-8 rounded-xl flex justify-center items-center gap-2 transition-all disabled:opacity-50">
                        <Save size={20} /> Register Union
                    </button>
                </form>
            ) : (
                <form onSubmit={handleDivorceSubmit} className="space-y-6">
                    <div>
                        <label className="block text-sm font-bold text-slate-700 mb-1">Marriage Certificate Number</label>
                        <input type="text" required value={divorceData.certificate_number} onChange={(e) => setDivorceData({...divorceData, certificate_number: e.target.value})} placeholder="e.g. MC-A1B2C3D4" className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-800 outline-none font-mono" />
                    </div>
                    <div>
                        <label className="block text-sm font-bold text-slate-700 mb-1">Date of Legal Divorce</label>
                        <input type="date" required value={divorceData.date_of_divorce} onChange={(e) => setDivorceData({...divorceData, date_of_divorce: e.target.value})} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-800 outline-none" />
                    </div>
                    <button type="submit" disabled={isLoading} className="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 px-8 rounded-xl flex justify-center items-center gap-2 transition-all disabled:opacity-50">
                        <HeartCrack size={20} /> Process Separation
                    </button>
                </form>
            )}
        </div>
    );
};

export default MarriageRegistry;