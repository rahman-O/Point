import React, { useContext, useEffect, useState } from 'react';
import { NavLink, useLocation } from 'react-router-dom';
import {
	Navbar,
	NavbarBrand,
	NavbarContent,
	NavbarItem,
} from '@nextui-org/navbar';
import { Image } from '@nextui-org/react';
import Logo from '../../../public/images/logoPoint.png';
import {
	FaBars,
	FaFacebook,
	FaTimes,
	FaTwitter,
	FaYoutube,
} from 'react-icons/fa';
import { Button } from 'flowbite-react';
import LangContext from '@/components/langContext/LangContext.jsx';
import classNames from 'classnames';

function NavBarPages() {
	const location = useLocation();
	const { lang, toggleLang } = useContext(LangContext);

	const [isMenuOpen, setIsMenuOpen] = useState(false);

	// Persist menu state on page refresh
	useEffect(() => {
		const storedMenuState = localStorage.getItem('isMenuOpen');
		if (storedMenuState) {
			setIsMenuOpen(JSON.parse(storedMenuState));
		}
	}, []);

	useEffect(() => {
		localStorage.setItem('isMenuOpen', JSON.stringify(isMenuOpen));

		// Lock scroll when menu is open
		if (isMenuOpen) {
			document.body.classList.add('no-scroll');
		} else {
			document.body.classList.remove('no-scroll');
		}
	}, [isMenuOpen]);

	const toggleMenu = () => {
		setIsMenuOpen((prev) => !prev);
	};

	const handleLinkClick = () => {
		setIsMenuOpen(false);
	};

	const NavItem = ({ pageName, href }) => {
		const isActive = location.pathname === href;
		return (
			<NavbarItem isActive={isActive}>
				<NavLink
					to={href}
					onClick={handleLinkClick}
					className={classNames(
						'transition duration-300 ease-in-out transform',
						isActive ? 'text-white' : 'hover:text-white text-lime-500'
					)}
					aria-current={isActive ? 'page' : undefined}
				>
					{pageName}
				</NavLink>
			</NavbarItem>
		);
	};

	return (
		<Navbar className='bg-black w-full'>
			<div className='flex items-center justify-between w-full'>
				<NavbarBrand>
					<Image alt='logo' className='w-12' src={Logo} />
					<p className='font-bold text-2xl text-white px-2'>POINT</p>
				</NavbarBrand>

				{/* Hamburger menu for small screens */}
				<div className='sm:hidden'>
					<button
						onClick={toggleMenu}
						className='text-white z-50 fixed top-4 left-4'
						aria-label='Toggle menu'
					>
						{isMenuOpen ? (
							<FaTimes className='text-3xl' />
						) : (
							<FaBars className='text-3xl' />
						)}
					</button>
				</div>

				<div
					className={classNames(
						'fixed top-0 left-0 h-full w-full bg-gradient-to-r from-blue-900 via-teal-800 to-teal-500 z-40 transition-all duration-500 ease-in-out',
						{ 'translate-x-0': isMenuOpen, '-translate-x-full': !isMenuOpen }
					)}
					style={{ height: '100vh', width: '100vw' }}
				>
					<div className='grid justify-items-center gap-4 pt-4'>
						<NavItem
							pageName={lang === 'en' ? 'Home' : 'الصفحة الرئيسية'}
							href='/'
						/>
						<NavItem
							pageName={lang === 'en' ? 'News' : 'الأخبار'}
							href='/news'
						/>
						<NavItem
							pageName={lang === 'en' ? 'Speakers' : 'الاجندة'}
							href='/speakers'
						/>
						<NavItem
							pageName={lang === 'en' ? 'Programs' : 'البرامج'}
							href='/programs'
						/>
						<NavItem
							pageName={lang === 'en' ? 'Conference' : 'المؤتمر'}
							href='/conference'
						/>
						<NavItem
							pageName={lang === 'en' ? 'Stream' : 'البث'}
							href='/stream'
						/>

						<div className='flex gap-4 mt-8'>
							<FaTwitter className='text-cyan-500 text-2xl' />
							<FaFacebook className='text-blue-500 text-2xl' />
							<FaYoutube className='text-red-500 text-2xl' />
						</div>

						<Button className='text-white mt-4' onClick={toggleLang}>
							{lang === 'en' ? 'العربية' : 'English'}
						</Button>
					</div>
				</div>

				{/* Regular navbar for larger screens */}
				<NavbarContent className='hidden sm:flex' justify='center'>
					<NavItem
						pageName={lang === 'en' ? 'Home' : 'الصفحة الرئيسية'}
						href='/'
					/>
					<NavItem pageName={lang === 'en' ? 'News' : 'الأخبار'} href='/news' />
					<NavItem
						pageName={lang === 'en' ? 'Speakers' : 'الاجندة'}
						href='/speakers'
					/>
					<NavItem
						pageName={lang === 'en' ? 'Programs' : 'البرامج'}
						href='/programs'
					/>
					<NavItem
						pageName={lang === 'en' ? 'Conference' : 'المؤتمر'}
						href='/conference'
					/>
					<NavItem
						pageName={lang === 'en' ? 'Stream' : 'البث'}
						href='/stream'
					/>
				</NavbarContent>

				<NavbarContent justify='end' className='hidden sm:flex'>
					<div className='flex gap-4'>
						<FaTwitter className='text-cyan-500 text-2xl' />
						<FaFacebook className='text-blue-500 text-2xl' />
						<FaYoutube className='text-red-500 text-2xl' />
					</div>

					<Button className='text-white ml-4' onClick={toggleLang}>
						{lang === 'en' ? 'العربية' : 'English'}
					</Button>
				</NavbarContent>
			</div>
		</Navbar>
	);
}

export default NavBarPages;
