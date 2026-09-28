import { Outlet } from 'react-router-dom';
import NavBarPages from '@/components/NavBarPages.jsx';
import FooterPages from '@/components/FooterPages.jsx';
import ScrollToTop from '@/components/ScrollToTop.jsx';
import { SearchProvider } from '@/components/search/SearchProvider.jsx';

export const Layout = ({}) => {
	return (
		<SearchProvider>
			<ScrollToTop />
			<NavBarPages />
			<div class='mb-16 overflow-hidden' style={{ minHeight: '60%' }}>
				<Outlet />
			</div>
			<FooterPages />
		</SearchProvider>
	);
};
