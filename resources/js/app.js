import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { useAuth } from './composables/useAuth';
import { getCsrfTokenFromMeta } from './services/csrf';
import axios from 'axios';

// Função para estabelecer sessão e obter token CSRF
const establishSession = async () => {
    try {
        // Faz uma requisição GET para estabelecer a sessão
        // Isso garante que os cookies de sessão sejam enviados antes de qualquer POST
        const response = await axios.get('/api/csrf-token', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        
        // Atualiza o meta tag com o token da sessão atual (importante!)
        if (response.data?.token) {
            const metaTag = document.head?.querySelector('meta[name="csrf-token"]') || 
                           document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.content = response.data.token;
            }
        }
        
        return response.data?.token || null;
    } catch (error) {
        console.warn('Erro ao estabelecer sessão:', error);
        return null;
    }
};

// Função para inicializar aplicação
const initializeApp = async () => {
    // Estabelecer sessão primeiro (importante para CSRF funcionar)
    await establishSession();
    
    // Aguardar um momento para garantir que a sessão foi estabelecida
    await new Promise(resolve => setTimeout(resolve, 100));
    
    // Token já foi atualizado no meta tag pela função establishSession
    
    // Montar aplicação
    const app = createApp(App);
    app.use(router);

    // Verificar autenticação ao iniciar
    const auth = useAuth();
    auth.checkAuth();
    app.mount('#app');
};

// Aguardar DOM carregar antes de inicializar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    initializeApp();
}
