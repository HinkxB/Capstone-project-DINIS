import { useState } from 'react';
import { Globe, Save, FileText } from 'lucide-react';
import api from '../api';

const AlienRegistration = () => {
    const [formData, setFormData] = useState({
        first_name: '',other_names: '', last_name: '', date_of_birth: '', sex: 'Male',
        nrc_type: 'Pink', // Pink = Alien, Blue = Refugee
        country_of_origin: '', passport_number: '',
        district_of_birth: '',
        village: 'N/A', chief: 'N/A', district: 'Lusaka' // Foreigners might not have traditional villages
    });
    
    const [isLoading, setIsLoading] = useState(false);
    const [result, setResult] = useState(null);

    const handleChange = (e) => setFormData({ ...formData, [e.target.name]: e.target.value });

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);
        setResult(null);

        try {
            const response = await api.post('/citizens/register', formData); 
            
            // ADD THIS LINE: This will print Laravel's exact response to your browser console!
            console.log("LARAVEL RESPONSE:", response.data); 
            
            setResult({ type: 'success', data: response.data });
            setFormData({ ...formData, first_name: '', last_name: '', passport_number: '' });
        } catch (error) { 
            // YOUR MISSING ERROR HANDLING WAS HERE
            setResult({ type: 'error', message: error.response?.data?.message || 'Failed to register.' });
        } finally {
            // Now finally can run safely!
            setIsLoading(false);
        }
    };

    return (
        <div className="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <div className="mb-8 border-b border-slate-100 pb-6">
                <h2 className="text-2xl font-black text-slate-800 flex items-center gap-2">
                    <Globe className={formData.nrc_type === 'Pink' ? 'text-pink-600' : 'text-blue-600'} /> 
                    Alien & Refugee Registration
                </h2>
                <p className="text-slate-500 mt-1">Issue Pink (Resident) or Blue (Refugee) NRCs to non-citizens.</p>
            </div>

            {result && result.type === 'success' && (
                <div className="mb-8 bg-green-50 p-6 rounded-xl border border-green-200 text-center">
                    <h3 className="text-green-800 font-bold text-lg mb-2">{result.data.message}</h3>
                    <p className="text-sm text-green-600 mb-2">Issued Document Number:</p>
                    <code className="bg-white px-4 py-2 rounded-lg font-mono text-xl border border-green-300 shadow-sm text-slate-800 block">
                        {result.data.nrc}
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
                    {/* ID Type Selection */}
                    <div className="col-span-1 md:col-span-2 flex gap-4">
                        <label className={`flex-1 border-2 rounded-xl p-4 cursor-pointer transition-all ${formData.nrc_type === 'Pink' ? 'border-pink-500 bg-pink-50' : 'border-slate-200 hover:border-pink-300'}`}>
                            <input type="radio" name="nrc_type" value="Pink" checked={formData.nrc_type === 'Pink'} onChange={handleChange} className="hidden" />
                            <span className="font-bold text-pink-700 block text-lg">Pink NRC</span>
                            <span className="text-sm text-pink-600">Legal Resident Alien / Foreign Spouse</span>
                        </label>
                        <label className={`flex-1 border-2 rounded-xl p-4 cursor-pointer transition-all ${formData.nrc_type === 'Blue' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-blue-300'}`}>
                            <input type="radio" name="nrc_type" value="Blue" checked={formData.nrc_type === 'Blue'} onChange={handleChange} className="hidden" />
                            <span className="font-bold text-blue-700 block text-lg">Blue NRC</span>
                            <span className="text-sm text-blue-600">Officially Recognized Refugee</span>
                        </label>
                    </div>

                    {/* International Details */}
                    <div className="space-y-4">
                        <h3 className="text-sm font-bold text-slate-400 uppercase flex items-center gap-2">
                            <Globe size={16} /> Origin Details
                        </h3>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Country of Origin</label>
                            <input type="text" name="country_of_origin" required value={formData.country_of_origin} onChange={handleChange} placeholder="e.g. Zimbabwe" className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-500 outline-none" />
                        </div>
                        <div>
                                {/* WE ADDED THIS FIELD */}
                                <label className="block text-sm font-medium text-slate-700 mb-1">City/District of Birth</label>
                                <input type="text" name="district_of_birth" required value={formData.district_of_birth} onChange={handleChange} placeholder="e.g. Harare" className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-500 outline-none" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Passport / Travel Document #</label>
                            <input type="text" name="passport_number" required value={formData.passport_number} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-500 outline-none uppercase" />
                        </div>
                    </div>

                    {/* Personal Details */}
                    <div className="space-y-4">
                        <h3 className="text-sm font-bold text-slate-400 uppercase flex items-center gap-2">
                            <FileText size={16} /> Personal Details
                        </h3>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-1">First Name</label>
                                <input type="text" name="first_name" required value={formData.first_name} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-500 outline-none" />
                            </div>
                            <div>
                                {/* WE ADDED THIS FIELD */}
                                <label className="block text-sm font-medium text-slate-700 mb-1">Other Names</label>
                                <input type="text" name="other_names" value={formData.other_names} onChange={handleChange} placeholder="(Optional)" className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-1">Last Name</label>
                                <input type="text" name="last_name" required value={formData.last_name} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-500 outline-none" />
                            </div>
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-1">Date of Birth</label>
                                <input type="date" name="date_of_birth" required value={formData.date_of_birth} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-500 outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-1">Sex</label>
                                <select name="sex" value={formData.sex} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-500 outline-none">
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="pt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit" disabled={isLoading} className={`text-white font-bold py-3 px-8 rounded-xl flex items-center gap-2 transition-all disabled:opacity-50 ${formData.nrc_type === 'Pink' ? 'bg-pink-600 hover:bg-pink-700' : 'bg-blue-600 hover:bg-blue-700'}`}>
                        <Save size={20} /> {isLoading ? 'Processing...' : `Issue ${formData.nrc_type} NRC`}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default AlienRegistration;