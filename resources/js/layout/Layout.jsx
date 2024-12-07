import { Outlet } from 'react-router-dom';
import NavBarPages from '@/components/NavBarPages.jsx';
import FooterPages from '@/Components/FooterPages.jsx';

export const Layout = ({}) => {
	return (
		<>
			<NavBarPages />
			<div class='mb-16'>
				<Outlet />
			</div>
			<FooterPages />
		</>
	);
};
