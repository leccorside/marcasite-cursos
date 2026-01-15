import { ref, computed } from 'vue';
import { authService } from '@/services/auth';
import { useRouter } from 'vue-router';

// Estado global compartilhado
const user = ref(null);
const loading = ref(false);

export function useAuth() {
    const router = useRouter();

    /**
     * Verificar autenticação
     */
    const checkAuth = async () => {
        loading.value = true;
        try {
            const result = await authService.checkAuth();
            if (result.success && result.user) {
                user.value = result.user;
            } else {
                user.value = null;
            }
        } catch (error) {
            user.value = null;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Login
     */
    const login = async (email, password) => {
        loading.value = true;
        try {
            const result = await authService.login(email, password);
            if (result.success) {
                user.value = result.data.user;
                await router.push(result.data.redirect || '/');
                return { success: true };
            }
            return result;
        } catch (error) {
            return {
                success: false,
                message: 'Erro ao fazer login',
            };
        } finally {
            loading.value = false;
        }
    };

    /**
     * Registro
     */
    const register = async (data) => {
        loading.value = true;
        try {
            const result = await authService.register(data);
            if (result.success) {
                user.value = result.data.user;
                await router.push(result.data.redirect || '/');
                return { success: true };
            }
            return result;
        } catch (error) {
            return {
                success: false,
                message: 'Erro ao fazer cadastro',
            };
        } finally {
            loading.value = false;
        }
    };

    /**
     * Logout
     */
    const logout = async () => {
        loading.value = true;
        try {
            await authService.logout();
            user.value = null;
            await router.push('/login');
        } catch (error) {
            // Mesmo com erro, limpar estado local
            user.value = null;
            await router.push('/login');
        } finally {
            loading.value = false;
        }
    };

    /**
     * Recuperar senha
     */
    const forgotPassword = async (email) => {
        loading.value = true;
        try {
            const result = await authService.forgotPassword(email);
            return result;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Resetar senha
     */
    const resetPassword = async (token, email, password, passwordConfirmation) => {
        loading.value = true;
        try {
            const result = await authService.resetPassword(token, email, password, passwordConfirmation);
            return result;
        } finally {
            loading.value = false;
        }
    };

    const isAuthenticated = computed(() => !!user.value);
    const isAdmin = computed(() => user.value?.tipo === 'admin');
    const isAluno = computed(() => user.value?.tipo === 'aluno');

    return {
        user,
        loading,
        isAuthenticated,
        isAdmin,
        isAluno,
        checkAuth,
        login,
        register,
        logout,
        forgotPassword,
        resetPassword,
    };
}
