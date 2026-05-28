import { useState, useEffect } from 'react';
import { UserPlus, Shield, Activity, Building2, Lock, Users } from 'lucide-react';
import api from '../api';

const UserManagement = () => {
    const [users, setUsers] = useState([]);
    const [formData, setFormData] = useState({
        full_name: '', 
        username: '', 
        password: '', 
        role: 'dnr_officer', 
        department: ''
    });
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState(null);

    /**
     * 1. THE BULLETPROOF FETCH
     * We define it inside useEffect so React doesn't complain about dependencies.
     */
    useEffect(() => {
        const fetchUsers = async () => {
            try {
                const response = await api.get('/users');
                // We ensure data is an array before setting it to prevent .map() crashes
                setUsers(Array.isArray(response.data) ? response.data : []);
            } catch (err) { 
                console.error("Failed to fetch users:", err); 
            }
        };

        fetchUsers();
    }, []); 

    /**
     * 2. FORM SUBMISSION
     */
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setMessage(null);

        try {
            await api.post('/users/create', formData);
            setMessage({ type: 'success', text: `Account for ${formData.username} has been provisioned.` });
            
            // Reset form
            setFormData({ 
                full_name: '', 
                username: '', 
                password: '', 
                role: 'dnr_officer', 
                department: '' 
            });

            // Refresh the list to show the new user
            const refresh = await api.get('/users');
            setUsers(Array.isArray(refresh.data) ? refresh.data : []);

        } catch (err) {
            setMessage({ 
                type: 'error', 
                text: err.response?.data?.message || 'Failed to create system user.' 
            });
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="p-1 md:p-4 space-y-8">
            {/* Header Section */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-black text-slate-800 tracking-tight">System Access Control</h1>
                    <p className="text-slate-500 text-sm font-medium">Manage departmental roles and system audit permissions.</p>
                </div>
                <div className="flex items-center gap-3 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm">
                    <div className="bg-blue-50 p-2 rounded-xl text-blue-600">
                        <Users size={20} />
                    </div>
                    <div className="pr-4">
                        <div className="text-xs font-bold text-slate-400 uppercase">Total Personnel</div>
                        <div className="text-lg font-black text-slate-800 leading-none">{users.length}</div>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* CREATE USER FORM */}
                <div className="lg:col-span-1">
                    <div className="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 sticky top-4">
                        <h3 className="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                            <UserPlus className="text-blue-600" size={20} /> Provision New Account
                        </h3>
                        
                        {message && (
                            <div className={`p-4 rounded-xl mb-6 text-sm font-bold flex items-start gap-2 ${
                                message.type === 'success' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100'
                            }`}>
                                {message.text}
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <label className="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Full Name</label>
                                <input type="text" required value={formData.full_name} onChange={e => setFormData({...formData, full_name: e.target.value})} className="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="e.g. Chanda Kapambwe" />
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">System Username</label>
                                <input type="text" required value={formData.username} onChange={e => setFormData({...formData, username: e.target.value})} className="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="ckapambwe_dnr" />
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Access Role</label>
                                <select value={formData.role} onChange={e => setFormData({...formData, role: e.target.value})} className="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                                    <option value="dnr_officer">DNR Officer (Home Affairs)</option>
                                    <option value="auditor">System Auditor (Logs Only)</option>
                                    <option value="security_officer">Police / Immigration</option>
                                    <option value="health_provider">Birth Facility (Clinics)</option>
                                    <option value="welfare_partner">Orphan & Welfare</option>
                                    <option value="admin">Super Admin</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Assigned Department</label>
                                <input type="text" value={formData.department} onChange={e => setFormData({...formData, department: e.target.value})} className="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="e.g. Lusaka Central Station" />
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Temporary Password</label>
                                <input type="password" required value={formData.password} onChange={e => setFormData({...formData, password: e.target.value})} className="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all" />
                            </div>
                            <button disabled={loading} className="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-slate-800 transition-all flex items-center justify-center gap-2 shadow-lg shadow-slate-200 disabled:opacity-50">
                                <Lock size={18} /> {loading ? 'Provisioning...' : 'Activate Account'}
                            </button>
                        </form>
                    </div>
                </div>

                {/* USER LIST & MONITORING */}
                <div className="lg:col-span-2">
                    <div className="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                        <div className="p-6 border-b border-slate-100 flex justify-between items-center">
                            <h3 className="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <Shield className="text-blue-600" size={20} /> Personnel Directory
                            </h3>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead className="bg-slate-50/50 text-slate-500 text-[10px] font-black uppercase tracking-widest">
                                    <tr>
                                        <th className="px-6 py-4">Identity & Username</th>
                                        <th className="px-6 py-4">Department & Role</th>
                                        <th className="px-6 py-4">Status</th>
                                        <th className="px-6 py-4 text-right">Audit</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {users.map(u => (
                                        <tr key={u.id} className="hover:bg-slate-50/80 transition-colors group">
                                            <td className="px-6 py-4">
                                                <div className="font-bold text-slate-800">{u.full_name}</div>
                                                <div className="text-xs text-slate-400 font-medium italic">@{u.username}</div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <span className={`inline-block px-2.5 py-1 rounded-lg text-[10px] font-black uppercase mb-1.5 ${
                                                    u.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'
                                                }`}>
                                                    {u.role?.replace('_', ' ')}
                                                </span>
                                                <div className="text-xs text-slate-500 flex items-center gap-1 font-medium">
                                                    <Building2 size={12} className="text-slate-400" /> {u.department || 'General HQ'}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <span className="flex items-center gap-1.5 text-green-600 text-xs font-black uppercase tracking-tighter">
                                                    <span className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Authorized
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <button title="View User Logs" className="p-2 text-slate-300 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                                                    <Activity size={18} />
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                    {users.length === 0 && (
                                        <tr>
                                            <td colSpan="4" className="px-6 py-12 text-center text-slate-400 font-medium italic">
                                                No personnel registered in the directory.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default UserManagement;