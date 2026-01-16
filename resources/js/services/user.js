import axios from 'axios';

export const userService = {
    /**
     * Listar usuários
     */
    async listar(params = {}) {
        try {
            const response = await axios.get('/api/usuarios', { params });
            return {
                success: true,
                data: response.data,
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao carregar usuários',
            };
        }
    }
};
