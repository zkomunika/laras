import { Crown, Sword, Shield, Flame, Flower2, Zap, Heart, Mountain, Moon, Wind } from 'lucide-vue-next';

const sundaFigures = [
    { name: 'Prabu Siliwangi', icon: Crown, color: '#fbbf24', bg: '#fffbeb' },
    { name: 'Kian Santang', icon: Sword, color: '#ef4444', bg: '#fef2f2' },
    { name: 'Niskala Wastu', icon: Shield, color: '#3b82f6', bg: '#eff6ff' },
    { name: 'Surawisesa', icon: Flame, color: '#f97316', bg: '#fff7ed' },
    { name: 'Ratu Pucuk Umun', icon: Flower2, color: '#ec4899', bg: '#fdf2f8' },
    { name: 'Borosngora', icon: Zap, color: '#8b5cf6', bg: '#f5f3ff' },
    { name: 'Ambetkasih', icon: Heart, color: '#f43f5e', bg: '#fff1f2' },
    { name: 'Walangsungsang', icon: Mountain, color: '#10b981', bg: '#ecfdf5' },
    { name: 'Rara Santang', icon: Moon, color: '#6366f1', bg: '#eef2ff' },
    { name: 'Mundinglaya', icon: Wind, color: '#06b6d4', bg: '#ecfeff' }
];

export function getUserAvatar(identifier) {
    if (!identifier) return sundaFigures[0];
    
    let hash = 0;
    for (let i = 0; i < identifier.length; i++) {
        hash = identifier.charCodeAt(i) + ((hash << 5) - hash);
    }
    
    const index = Math.abs(hash) % sundaFigures.length;
    return sundaFigures[index];
}
