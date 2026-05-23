import axios from 'axios';

// The proxy path we set up in vite.config.js
const FIREFLY_BASE_URL = '/api/v1/namespaces/default/apis/zambia-nrc-api';

const apiClient = axios.create({
  baseURL: FIREFLY_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// MAKE SURE THIS LINE HAS 'export const'
export const nrcService = {
  registerCitizen: async (citizenData) => {
    try {
      const response = await apiClient.post('/invoke/RegisterCitizen', {
        input: citizenData
      });
      return response.data;
    } catch (error) {
      console.error("Error registering citizen:", error);
      throw error;
    }
  },

  readIdentity: async (nrcNumber) => {
    try {
      const response = await apiClient.post('/query/ReadIdentity', {
        input: { nrcNumber }
      });
      return response.data;
    } catch (error) {
      console.error("Error fetching identity:", error);
      throw error;
    }
  }
};
