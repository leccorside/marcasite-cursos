import axios from 'axios';

export const cursoService = {
    /**
     * Listar cursos
     */
    async listar(params = {}) {
        try {
            const response = await axios.get('/api/cursos', { params });
            return {
                success: true,
                data: response.data,
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao listar cursos',
                errors: error.response?.data?.errors || {},
            };
        }
    },

    /**
     * Listar cursos para a vitrine pública
     */
    async listarPublicos(params = {}) {
        try {
            const response = await axios.get('/api/public/cursos', { params });
            return {
                success: true,
                data: response.data,
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao carregar vitrine',
            };
        }
    },

    /**
     * Listar categorias únicas dos cursos ativos
     */
    async listarCategorias() {
        try {
            const response = await axios.get('/api/public/categorias');
            return {
                success: true,
                data: response.data,
            };
        } catch (error) {
            return {
                success: false,
                message: 'Erro ao carregar categorias',
            };
        }
    },

    /**
     * Buscar um curso específico
     */
    async buscar(id) {
        try {
            const response = await axios.get(`/api/cursos/${id}`);
            return {
                success: true,
                data: response.data,
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao buscar curso',
            };
        }
    },

    /**
     * Listar inscritos de um curso
     */
    async listarInscritos(id) {
        try {
            const response = await axios.get(`/api/cursos/${id}/inscritos`);
            return {
                success: true,
                data: response.data,
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao listar inscritos',
            };
        }
    },

    /**
     * Criar curso
     */
    async criar(dados) {
        try {
            // Se dados for um objeto comum, transforma em FormData para suportar arquivos
            let payload = dados;
            if (!(dados instanceof FormData)) {
                payload = new FormData();
                Object.keys(dados).forEach(key => {
                    if (dados[key] !== null && dados[key] !== undefined) {
                        if (Array.isArray(dados[key])) {
                            dados[key].forEach(item => payload.append(`${key}[]`, item));
                        } else {
                            payload.append(key, dados[key]);
                        }
                    }
                });
            }

            const response = await axios.post('/api/cursos', payload, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            return {
                success: true,
                data: response.data.data,
                message: response.data.message || 'Curso criado com sucesso',
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao criar curso',
                errors: error.response?.data?.errors || {},
            };
        }
    },

    /**
     * Atualizar curso
     */
    async atualizar(id, dados) {
        try {
            // Laravel bug: PUT/PATCH não funciona com FormData (arquivos).
            // Usamos POST e spoofing de método com _method: 'PUT'
            let payload = dados;
            if (dados instanceof FormData) {
                if (!dados.has('_method')) {
                    dados.append('_method', 'PUT');
                }
            }

            const response = await axios.post(`/api/cursos/${id}`, payload, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            
            return {
                success: true,
                data: response.data.data,
                message: response.data.message || 'Curso atualizado com sucesso',
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao atualizar curso',
                errors: error.response?.data?.errors || {},
            };
        }
    },

    /**
     * Excluir curso
     */
    async excluir(id) {
        try {
            const response = await axios.delete(`/api/cursos/${id}`);
            return {
                success: true,
                message: response.data.message || 'Curso excluído com sucesso',
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao excluir curso',
            };
        }
    },
};
