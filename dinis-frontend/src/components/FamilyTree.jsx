// src/components/FamilyTree.jsx
import { useState } from 'react';
import { Search, Network, User, } from 'lucide-react';
import api from '../api';


// A helper component to draw a standard "Person Card"
    const PersonCard = ({ name, nrc, role, gender }) => (
        <div className={`p-4 rounded-xl border-2 w-48 text-center shadow-sm ${
            gender === 'M' ? 'border-blue-200 bg-blue-50' : 'border-pink-200 bg-pink-50'
        }`}>
            <div className="w-10 h-10 mx-auto rounded-full bg-white flex items-center justify-center mb-2 shadow-sm">
                <User size={20} className={gender === 'M' ? 'text-blue-500' : 'text-pink-500'} />
            </div>
            <p className="font-bold text-slate-800 text-sm truncate" title={name}>{name}</p>
            <p className="text-xs font-mono text-slate-500 mt-1">{nrc}</p>
            {role && <span className="inline-block mt-2 text-[10px] uppercase font-black tracking-widest text-white bg-slate-800 px-2 py-1 rounded-full">{role}</span>}
        </div>
    );

const FamilyTree = () => {
    // State for the search bar
    const [searchNrc, setSearchNrc] = useState('');
    
    // State to hold the API response
    const [treeData, setTreeData] = useState(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState('');

    // Fetch tree data when the user searches an NRC
    const handleSearch = async (e) => {
        e.preventDefault();
        if (!searchNrc) return;

        setIsLoading(true);
        setError('');
        setTreeData(null);

        try {
            // This hits the new Laravel endpoint we just created
            const response = await api.get(`/family-tree/${encodeURIComponent(searchNrc)}`);
            setTreeData(response.data);
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to fetch family tree.');
        } finally {
            setIsLoading(false);
        }
    };

    

    return (
        <div className="max-w-5xl mx-auto space-y-6">
            
            {/* Search Header */}
            <div className="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row gap-4 justify-between items-center">
                <div>
                    <h2 className="text-2xl font-black text-slate-800 flex items-center gap-2">
                        <Network className="text-purple-600" /> Family Lineage Viewer
                    </h2>
                    <p className="text-sm text-slate-500">Enter a citizen's NRC to visualize their immediate family tree.</p>
                </div>

                <form onSubmit={handleSearch} className="flex gap-2 w-full md:w-auto">
                    <input 
                        type="text" 
                        value={searchNrc} 
                        onChange={(e) => setSearchNrc(e.target.value)} 
                        placeholder="e.g. 123456/78/1" 
                        className="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none w-full md:w-64"
                    />
                    <button 
                        type="submit" 
                        disabled={isLoading}
                        className="bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-xl transition-all disabled:opacity-50"
                    >
                        <Search size={20} />
                    </button>
                </form>
            </div>

            {/* Error Message */}
            {error && (
                <div className="bg-red-50 text-red-700 p-4 rounded-xl border border-red-200 font-medium">
                    {error}
                </div>
            )}

            {/* Visual Tree Rendering */}
            {/* Visual Tree Rendering (Genealogical Format) */}
            {treeData && (
                <div className="bg-white p-12 rounded-2xl shadow-sm border border-slate-200 overflow-x-auto flex flex-col items-center">
                    
                    {/* LEVEL 1: PARENTS (Side-by-Side Marriage Link) */}
                    {treeData.parents.length > 0 && (
                        <div className="flex items-start justify-center relative">
                            {/* Mother */}
                            {treeData.parents.find(p => p.sex === 'F') && (
                                <PersonCard 
                                    name={treeData.parents.find(p => p.sex === 'F').maiden_full_name} 
                                    nrc={treeData.parents.find(p => p.sex === 'F').nrc_number} 
                                    role="MOTHER" 
                                    gender="F" 
                                />
                            )}
                            
                            {/* Marriage Connecting Line & Dropdown Line */}
                            {treeData.parents.length === 2 && (
                                <div className="w-16 h-0.5 bg-slate-400 mt-12 relative">
                                    {/* Divorce status logic will go here in the future */}
                                    <div className="absolute top-0 left-1/2 w-0.5 h-12 bg-slate-400 -translate-x-1/2"></div>
                                </div>
                            )}

                            {/* Father */}
                            {treeData.parents.find(p => p.sex === 'M') && (
                                <PersonCard 
                                    name={treeData.parents.find(p => p.sex === 'M').maiden_full_name} 
                                    nrc={treeData.parents.find(p => p.sex === 'M').nrc_number} 
                                    role="FATHER" 
                                    gender="M" 
                                />
                            )}
                        </div>
                    )}

                    {/* TARGET CITIZEN */}
                    <div className="mt-4 ring-4 ring-purple-200 rounded-xl relative z-10 bg-white">
                        <PersonCard 
                            name={treeData.target.maiden_full_name} 
                            nrc={treeData.target.nrc_number} 
                            role="TARGET" 
                            gender={treeData.target.sex} 
                        />
                    </div>

                    {/* Vertical line dropping from target to children */}
                    {treeData.children.length > 0 && (
                        <div className="w-0.5 h-8 bg-slate-400"></div>
                    )}

                    {/* LEVEL 3: CHILDREN (With horizontal spanning line) */}
                    {treeData.children.length > 0 && (
                        <div className="relative pt-4 flex gap-8 justify-center">
                            
                            {/* Horizontal line spanning across all children */}
                            {treeData.children.length > 1 && (
                                <div 
                                    className="absolute top-0 h-0.5 bg-slate-400" 
                                    // Dynamically width based on number of children to stop at the last child's center
                                    style={{ left: 'calc(50% / ' + treeData.children.length + ')', right: 'calc(50% / ' + treeData.children.length + ')' }}
                                ></div>
                            )}

                            {treeData.children.map((child, idx) => (
                                <div key={idx} className="relative flex flex-col items-center">
                                    {/* Small vertical line connecting to the horizontal span */}
                                    <div className="w-0.5 h-4 bg-slate-400 absolute -top-4"></div>
                                    <PersonCard 
                                        name={child.maiden_full_name} 
                                        nrc={child.nrc_number} 
                                        role="CHILD" 
                                        gender={child.sex} 
                                    />
                                </div>
                            ))}
                        </div>
                    )}
                    
                </div>
            )}
        </div>
    );
};

export default FamilyTree;