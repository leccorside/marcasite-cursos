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
            console.error('Erro ao listar usuários:', error.response?.status, error.response?.data || error.message);
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao carregar usuários',
                status: error.response?.status,
            };
        }
    },

    /**
     * Criar novo usuário
     */
    async criar(dados) {
        try {
            const response = await axios.post('/api/usuarios', dados, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            return {
                success: true,
                message: response.data.message,
                data: response.data.data,
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao criar usuário',
                errors: error.response?.data?.errors || {},
            };
        }
    },

    /**
     * Atualizar usuário
     */
    async atualizar(id, dados) {
        try {
            // Laravel workaround para enviar FormData com PUT
            if (dados instanceof FormData) {
                dados.append('_method', 'PUT');
            }
            const response = await axios.post(`/api/usuarios/${id}`, dados, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            return {
                success: true,
                message: response.data.message,
                data: response.data.data,
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao atualizar usuário',
                errors: error.response?.data?.errors || {},
            };
        }
    },

    /**
     * Excluir usuário
     */
    async excluir(id) {
        try {
            const response = await axios.delete(`/api/usuarios/${id}`);
            return {
                success: true,
                message: response.data.message,
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao excluir usuário',
            };
        }
    }
};
