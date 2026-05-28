import { useState } from 'react';
import { Save, Building2, User, MapPin } from 'lucide-react';
import api from '../api';

const OrphanRegistry = () => {
    const [formData, setFormData] = useState({
        first_name: '', last_name: '', date_of_birth: '',
        sex: 'Male', village: '', chief: '', institution_name: ''
    });
    
    const [isLoading, setIsLoading] = useState(false);
    const [result, setResult] = useState(null);

    const handleChange = (e) => setFormData({ ...formData, [e.target.name]: e.target.value });

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);
        setResult(null);

        try {
            const response = await api.post('/orphans', formData);
            setResult({ type: 'success', data: response.data });
            setFormData({ first_name: '', last_name: '', date_of_birth: '', sex: 'Male', village: '', chief: '', institution_name: '' });
        } catch (error) {
            setResult({ type: 'error', message: error.response?.data?.message || 'Failed to register orphan.' });
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div className="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <div className="mb-8 border-b border-slate-100 pb-6">
                <h2 className="text-2xl font-black text-slate-800 flex items-center gap-2">
                    <Building2 className="text-orange-600" /> Orphan & Welfare Registry
                </h2>
                <p className="text-slate-500 mt-1">Register institutionalized children to generate a system clearance ID.</p>
            </div>

            {result && result.type === 'success' && (
                <div className="mb-8 bg-green-50 p-6 rounded-xl border border-green-200 text-center">
                    <h3 className="text-green-800 font-bold text-lg mb-2">{result.data.message}</h3>
                    <p className="text-sm text-green-600 mb-2">Provide this UUID to the citizen when they turn 18 to claim their NRC:</p>
                    <code className="bg-white px-4 py-2 rounded-lg font-mono text-xl border border-green-300 shadow-sm text-slate-800 block">
                        {result.data.orphan_id}
                    </code>
                </div>
            )}

            {result && result.type === 'error' && (
                <div className="mb-8 bg-red-50 p-4 rounded-xl border border-red-200 text-red-700 font-medium">
                    {result.message}
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {/* Personal Details */}
                    <div className="space-y-4">
                        <h3 className="text-sm font-bold text-slate-400 uppercase flex items-center gap-2">
                            <User size={16} /> Child Details
                        </h3>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">First Name</label>
                            <input type="text" name="first_name" required value={formData.first_name} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Last Name</label>
                            <input type="text" name="last_name" required value={formData.last_name} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none" />
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-1">Date of Birth</label>
                                <input type="date" name="date_of_birth" required value={formData.date_of_birth} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-1">Sex</label>
                                <select name="sex" value={formData.sex} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none">
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Geography & Institution */}
                    <div className="space-y-4">
                        <h3 className="text-sm font-bold text-slate-400 uppercase flex items-center gap-2">
                            <MapPin size={16} /> Welfare Information
                        </h3>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Reporting Institution</label>
                            <input type="text" name="institution_name" required placeholder="e.g. Kasisi Orphanage" value={formData.institution_name} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Village of Origin / Discovery</label>
                            <input type="text" name="village" required value={formData.village} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Chiefdom</label>
                            <input type="text" name="chief" required value={formData.chief} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none" />
                        </div>
                    </div>
                </div>

                <div className="pt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit" disabled={isLoading} className="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-8 rounded-xl flex items-center gap-2 transition-all disabled:opacity-50">
                        <Save size={20} /> {isLoading ? 'Generating ID...' : 'Register Orphan'}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default OrphanRegistry;