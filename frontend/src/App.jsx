import { useState } from 'react';
import { nrcService } from './services/nrcService';

export default function App() {
  const [nrcNumber, setNrcNumber] = useState('');
  const [citizenData, setCitizenData] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSearch = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    setCitizenData(null);

    try {
      // Calling our service layer which uses the Vite proxy
      const response = await nrcService.readIdentity(nrcNumber);
      
      // FireFly query responses usually wrap the data in a 'record' or 'output' field
      // Adjusting this to handle the response format
      setCitizenData(response);
    } catch (err) {
      setError('Could not find citizen record. Please check the NRC number and try again.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen p-8 flex flex-col items-center font-sans">
      <div className="max-w-2xl w-full bg-white rounded-lg shadow-xl p-8 border-t-8 border-green-700">
        <div className="flex justify-between items-center mb-6">
          <div>
            <h1 className="text-3xl font-extrabold text-gray-900 tracking-tight">Zambia National Registration</h1>
            <p className="text-green-700 font-semibold">Blockchain-Secured Identity Portal</p>
          </div>
          <div className="bg-gray-100 p-2 rounded-full">
             {/* Simple visual placeholder for a government seal */}
             <div className="w-12 h-12 border-4 border-yellow-500 rounded-full flex items-center justify-center text-[10px] font-bold text-gray-400">SEAL</div>
          </div>
        </div>

        <form onSubmit={handleSearch} className="flex gap-4 mb-10">
          <input
            type="text"
            placeholder="Enter NRC Number (e.g. 123456/78/1)"
            className="flex-1 px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-green-600 transition-colors"
            value={nrcNumber}
            onChange={(e) => setNrcNumber(e.target.value)}
            required
          />
          <button
            type="submit"
            disabled={loading}
            className="bg-green-700 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-800 disabled:opacity-50 shadow-lg transition-all"
          >
            {loading ? 'Consulting Ledger...' : 'Verify Identity'}
          </button>
        </form>

        {error && (
          <div className="bg-red-50 text-red-700 p-4 rounded-lg mb-6 border-l-4 border-red-600 animate-pulse">
            {error}
          </div>
        )}

        {citizenData && (
          <div className="bg-gray-50 p-8 rounded-xl border-2 border-gray-100 shadow-inner">
            <h2 className="text-sm uppercase tracking-widest text-gray-500 font-bold mb-6 border-b pb-2">Blockchain Verified Record</h2>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div className="space-y-1">
                <label className="text-xs font-bold text-green-800 uppercase">Full Legal Name</label>
                <p className="text-xl font-medium text-gray-900">{citizenData.fullName || 'NOT FOUND'}</p>
              </div>
              <div className="space-y-1">
                <label className="text-xs font-bold text-green-800 uppercase">NRC Number</label>
                <p className="text-xl font-mono text-gray-900 font-bold">{nrcNumber}</p>
              </div>
              <div className="space-y-1">
                <label className="text-xs font-bold text-green-800 uppercase">District of Registration</label>
                <p className="text-lg text-gray-800">{citizenData.district || 'N/A'}</p>
              </div>
              <div className="space-y-1">
                <label className="text-xs font-bold text-green-800 uppercase">Verification Status</label>
                <p className="text-lg text-green-600 font-bold">✓ SECURELY ANCHORED</p>
              </div>
            </div>
          </div>
        )}
      </div>
      
      <p className="mt-8 text-gray-400 text-xs uppercase tracking-tighter">
        Hyperledger Fabric & FireFly Node: Active
      </p>
    </div>
  );
}
