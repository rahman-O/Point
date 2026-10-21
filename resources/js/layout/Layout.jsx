import { useEffect, useRef } from 'react';
import { Outlet, useLocation } from 'react-router-dom';
import NavBarPages from '@/components/NavBarPages.jsx';
import FooterPages from '@/components/FooterPages.jsx';
import ScrollToTop from '@/components/ScrollToTop.jsx';
import { SearchProvider } from '@/components/search/SearchProvider.jsx';

const SITE_TITLE = 'مؤتمر بوينت العراق | Point Iraq Conference';

export const Layout = ({}) => {
	const { pathname } = useLocation();
	const isFirstPage = useRef(true);

	// The server renders a page-specific <title> on full loads; after in-app navigation fall
	// back to the site name (the search page manages its own title).
	useEffect(() => {
		if (isFirstPage.current) {
			isFirstPage.current = false;
			return;
		}
		if (pathname !== '/search') document.title = SITE_TITLE;
	}, [pathname]);

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
