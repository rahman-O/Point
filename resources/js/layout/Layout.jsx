import { Outlet } from 'react-router-dom';
import NavBarPages from '@/components/NavBarPages.jsx';
import FooterPages from '@/Components/FooterPages.jsx';
import ScrollToTop from '@/components/ScrollToTop.jsx';

export const Layout = ({}) => {
	return (
		<>
			<ScrollToTop />
			<NavBarPages />
			<div class='mb-16 overflow-hidden' style={{ minHeight: '60%' }}>
				<Outlet />
			</div>
			<FooterPages />
		</>
	);
};
