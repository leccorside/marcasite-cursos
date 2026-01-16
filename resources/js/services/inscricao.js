import axios from 'axios';

export const inscricaoService = {
    /**
     * Listar inscrições do aluno logado
     */
    async listar(params = {}) {
        try {
            const response = await axios.get('/api/meus-cursos', { params });
            return {
                success: true,
                data: response.data,
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao carregar seus cursos',
            };
        }
    },

    /**
     * Criar nova inscrição
     */
    async criar(cursoId) {
        try {
            const response = await axios.post('/api/inscricoes', {
                curso_id: cursoId
            });
            return {
                success: true,
                data: response.data,
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao processar inscrição',
                errors: error.response?.data?.errors || {},
            };
        }
    }
};
