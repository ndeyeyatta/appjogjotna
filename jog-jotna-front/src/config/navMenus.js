export const navMenus = {
  parent: [
    { to: '/dashboard/parent', icon: '🏠', label: 'Accueil' },
    { to: '/enfants', icon: '👶', label: 'Enfants' },
    { to: '/observations/creer', icon: '✏', label: 'Signaler' },
    { to: '/profil', icon: '👤', label: 'Profil' },
  ],
  encadreur: [
    { to: '/dashboard/encadreur', icon: '🏠', label: 'Accueil' },
    { to: '/enfants', icon: '👶', label: 'Enfants' },
    { to: '/observations/validation', icon: '📋', label: 'Signalements' },
    { to: '/profil', icon: '👤', label: 'Profil' },
  ],
  responsable: [
    { to: '/dashboard/responsable', icon: '🏠', label: 'Accueil' },
    { to: '/rapports', icon: '📊', label: 'Rapports' },
    { to: '/alertes', icon: '⚠', label: 'Alertes' },
    { to: '/profil', icon: '👤', label: 'Profil' },
  ],
  admin: [
    { to: '/dashboard/responsable', icon: '🏠', label: 'Accueil' },
    { to: '/admin/utilisateurs', icon: '👥', label: 'Comptes' },
    { to: '/admin/config', icon: '⚙', label: 'Config' },
    { to: '/profil', icon: '👤', label: 'Profil' },
  ],
};

export function getNavItems(role) {
  return navMenus[role] || navMenus.parent;
}
