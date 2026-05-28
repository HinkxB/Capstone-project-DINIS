// src/components/RegisterCitizen.jsx
import { useState } from 'react';
import { Save, User, UserPlus, MapPin, Users, AlertCircle, CheckCircle2 } from 'lucide-react';
import api from '../api'; // Ensure this points to your configured Axios instance

const RegisterCitizen = () => {
    // ------------------------------------------------------------------------
    // 1. STATE MANAGEMENT
    // ------------------------------------------------------------------------
    // We store all form inputs in a single object to keep the code clean.
    const [formData, setFormData] = useState({
        first_name: '',
        last_name: '',
        other_names: '',
        date_of_birth: '',
        sex: '',
        village: '',
        chief: '',
        district_of_birth: '',
        residential_address: '',
        mother_nrc: '', // Used to link to the mother in the database
        father_nrc: '', // Used to link to the father in the database
    });

    // UI Feedback States
    const [isLoading, setIsLoading] = useState(false);
    const [message, setMessage] = useState({ type: '', text: '' });


    // --- NEW: ZAMBIAN NRC ELIGIBILITY CHECKER ---
    const calculateEligibility = (dob) => {
        if (!dob) return { eligible: false, age: 0, message: '' };
        
        const birthDate = new Date(dob);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        // Adjust age if their birthday hasn't happened yet this year
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        // Zambian Law: Must be 16 to acquire an NRC
        // if (age < 16) {
        //     return { eligible: false, age, message: `Ineligible: Citizen is only ${age} years old. Must be 16.` };
        // }
        return { eligible: true, age, message: `Eligible: Citizen is ${age} years old.` };
    };

    const eligibility = calculateEligibility(formData.date_of_birth);

    // ------------------------------------------------------------------------
    // 2. HANDLERS
    // ------------------------------------------------------------------------
    // This dynamically updates the correct field in state when a user types
    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
    };

    // Form submission logic
    const handleSubmit = async (e) => {
        e.preventDefault(); 
        setIsLoading(true);
        setMessage({ type: '', text: '' });

        try {
            // CRITICAL: Change '/citizens' to match your working route from RegisterCitizen.jsx
            const response = await api.post('/citizens', formData); 
            
            setMessage({ 
                type: 'success', 
                text: `Successfully issued ${formData.nrc_type} NRC: ${response.data.nrc_number}` 
            });
            
            // Optional: Clear form after success
            setFormData({
                first_name: '', last_name: '', date_of_birth: '', sex: 'Male',
                nrc_type: 'Pink', country_of_origin: '', passport_number: '',
                village: 'N/A', chief: 'N/A', district: 'Lusaka'
            });
        } catch (err) {
            // This captures the validation errors from Laravel
            const errorMsg = err.response?.data?.message || 'Failed to register foreign resident.';
            setMessage({ type: 'error', text: errorMsg });
        } finally {
            setIsLoading(false);
        }
    };

    // ------------------------------------------------------------------------
    // 3. UI RENDER
    // ------------------------------------------------------------------------
    return (
        <div className="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 max-w-4xl">
            <div className="mb-8 border-b border-slate-100 pb-4">
                <h2 className="text-2xl font-black text-slate-800 flex items-center gap-2">
                    <UserPlus className="text-blue-600" />
                    National Registration Form
                </h2>
                <p className="text-slate-500 mt-1 text-sm">
                    Enter the citizen's details according to the Zambian National Registration Act.
                </p>
            </div>

            {/* Status Messages (Success/Error) */}
            {message.text && (
                <div className={`p-4 rounded-xl mb-6 flex items-center gap-3 ${
                    message.type === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'
                }`}>
                    {message.type === 'error' ? <AlertCircle size={20} /> : <CheckCircle2 size={20} />}
                    <span className="font-medium">{message.text}</span>
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-8">
                
                {/* SECTION 1: Personal Details */}
                <section>
                    <h3 className="text-lg font-bold text-slate-700 flex items-center gap-2 mb-4">
                        <User size={18} className="text-slate-400" /> Personal Details
                    </h3>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-1">First Name *</label>
                            <input required type="text" name="first_name" value={formData.first_name} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                        </div>
                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-1">Last Name *</label>
                            <input required type="text" name="last_name" value={formData.last_name} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                        </div>
                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-1">Other Names</label>
                            <input type="text" name="other_names" value={formData.other_names} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                        </div>
                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-1">Date of Birth *</label>
                            <input required type="date" name="date_of_birth" value={formData.date_of_birth} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                        </div>
                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-1">Sex *</label>
                            <select required name="sex" value={formData.sex} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                </section>

                {/* SECTION 2: Place of Birth & Origin */}
                <section>
                    <h3 className="text-lg font-bold text-slate-700 flex items-center gap-2 mb-4">
                        <MapPin size={18} className="text-slate-400" /> Origin Details
                    </h3>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-1">Village *</label>
                            <input required type="text" name="village" value={formData.village} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                        </div>
                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-1">Chief *</label>
                            <input required type="text" name="chief" value={formData.chief} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                        </div>
                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-1">District of Birth *</label>
                            <input required type="text" name="district_of_birth" value={formData.district_of_birth} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                        </div>
                        <div className="md:col-span-3">
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-1">Current Residential Address *</label>
                            <input required type="text" name="residential_address" value={formData.residential_address} onChange={handleChange} className="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g., Plot 123, Cairo Road, Lusaka" />
                        </div>
                    </div>
                </section>

                {/* SECTION 3: Parental Linkage (For Family Tree) */}
                <section className="bg-blue-50/50 p-6 rounded-xl border border-blue-100">
                    <h3 className="text-lg font-bold text-blue-900 flex items-center gap-2 mb-4">
                        <Users size={18} className="text-blue-500" /> Family Linkage
                    </h3>
                    <p className="text-sm text-slate-500 mb-4">
                        Linking parent NRCs connects this citizen to the national family tree.
                    </p>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-1">Mother's NRC (Optional)</label>
                            <input type="text" name="mother_nrc" value={formData.mother_nrc} onChange={handleChange} placeholder="e.g., 123456/78/1" className="w-full p-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                        </div>
                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-1">Father's NRC (Optional)</label>
                            <input type="text" name="father_nrc" value={formData.father_nrc} onChange={handleChange} placeholder="e.g., 123456/78/1" className="w-full p-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                        </div>
                    </div>
                </section>

                {/* Submit Button & Eligibility Warning */}
                <div className="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        {formData.date_of_birth && (
                            <span className={`text-sm font-bold px-3 py-1 rounded-full ${eligibility.eligible ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                                {eligibility.message}
                            </span>
                        )}
                    </div>
                    <button 
                        type="submit" 
                        // Disable if loading OR if they are underage
                        disabled={isLoading || (formData.date_of_birth && !eligibility.eligible)}
                        className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl flex items-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {isLoading ? 'Saving Record...' : (
                            <>
                                <Save size={20} /> Register Citizen
                            </>
                        )}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default RegisterCitizen;