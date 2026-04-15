import { ref } from 'vue';
import { useRoute } from 'vue-router';

const appName = 'LegalFirm';

const seoState = ref({
  title: '',
  description: '',
  keywords: '',
  image: '',
  url: '',
  type: 'website'
});

const useSeo = () => {
  const route = useRoute();
  
  const setSeo = (options) => {
    const {
      title = '',
      description = '',
      keywords = '',
      image = '',
      url = '',
      type = 'website'
    } = options;

    seoState.value = {
      title,
      description,
      keywords,
      image,
      url: url || window.location.href,
      type
    };

    updateDocument(title, description, keywords, image, url || window.location.href, type);
  };

  const updateDocument = (title, description, keywords, image, url, type) => {
    const fullTitle = title ? `${title} | ${appName}` : appName;
    
    document.title = fullTitle;
    
    updateMetaTag('description', description);
    updateMetaTag('keywords', keywords);
    
    updateMetaTag('og:title', fullTitle, 'property');
    updateMetaTag('og:description', description, 'property');
    updateMetaTag('og:type', type, 'property');
    updateMetaTag('og:url', url, 'property');
    
    if (image) {
      const imageUrl = image.startsWith('http') ? image : window.location.origin + image;
      updateMetaTag('og:image', imageUrl, 'property');
    }
    
    updateMetaTag('twitter:card', 'summary_large_image');
    updateMetaTag('twitter:title', fullTitle);
    updateMetaTag('twitter:description', description);
    
    if (image) {
      const imageUrl = image.startsWith('http') ? image : window.location.origin + image;
      updateMetaTag('twitter:image', imageUrl);
    }
    
    let canonical = document.querySelector('link[rel="canonical"]');
    if (!canonical) {
      canonical = document.createElement('link');
      canonical.rel = 'canonical';
      document.head.appendChild(canonical);
    }
    canonical.href = url || window.location.href;
  };

  const updateMetaTag = (name, content, type = 'name') => {
    if (!content) return;
    
    const selector = type === 'property' 
      ? `meta[property="${name}"]` 
      : `meta[name="${name}"]`;
    
    let meta = document.querySelector(selector);
    
    if (!meta) {
      meta = document.createElement('meta');
      meta.setAttribute(type, name);
      meta.setAttribute('content', content);
      document.head.appendChild(meta);
    } else {
      meta.setAttribute('content', content);
    }
  };

  const clearSeo = () => {
    seoState.value = {
      title: '',
      description: '',
      keywords: '',
      image: '',
      url: '',
      type: 'website'
    };
    document.title = appName;
  };

  const initRouteSeo = () => {
    const routeName = route.name?.toString() || '';
    const seoData = getRouteSeoData(routeName);
    if (seoData) {
      setSeo(seoData);
    }
  };

  return {
    seoState,
    setSeo,
    clearSeo,
    initRouteSeo
  };
};

const getRouteSeoData = (routeName) => {
  const seoMap = {
    'dashboard': {
      title: 'Dashboard',
      description: 'Legal practice management dashboard. Monitor cases, clients, billing, tasks, and team performance.'
    },
    'cases': {
      title: 'Cases',
      description: 'Manage all legal cases, track status, and organize case files.'
    },
    'clients': {
      title: 'Clients',
      description: 'Manage client information, contacts, and communication history.'
    },
    'leads': {
      title: 'Leads',
      description: 'Track potential clients and manage lead conversion pipeline.'
    },
    'invoices': {
      title: 'Invoices',
      description: 'Manage billing, create invoices, and track payments.'
    },
    'tasks': {
      title: 'Tasks',
      description: 'Manage tasks, deadlines, and team assignments.'
    },
    'documents': {
      title: 'Documents',
      description: 'Legal document management, templates, and generating documents.'
    },
    'calendar': {
      title: 'Calendar',
      description: 'Schedule management, deadlines, and court dates.'
    },
    'crm': {
      title: 'CRM',
      description: 'Client relationship management, interactions, and follow-ups.'
    },
    'settings': {
      title: 'Settings',
      description: 'Configure application settings, users, and preferences.'
    },
    'billing': {
      title: 'Billing & Expenses',
      description: 'Track expenses, income, and financial reports.'
    },
    'reports': {
      title: 'Reports',
      description: 'Generate legal practice reports and analytics.'
    },
    'trust-accounts': {
      title: 'Trust Accounts',
      description: 'Manage client trust accounts and ledgers.'
    },
    'login': {
      title: 'Login',
      description: 'Sign in to your legal practice management account.',
      type: 'website'
    },
    'register': {
      title: 'Register',
      description: 'Create your legal practice management account.',
      type: 'website'
    },
    'home': {
      title: 'Legal Practice Management',
      description: 'Comprehensive legal practice management software for law firms. Manage cases, clients, billing, documents, and more.',
      keywords: 'legal case management, law firm software, legal CRM, attorney management'
    }
  };

  return seoMap[routeName] || null;
};

export default useSeo;