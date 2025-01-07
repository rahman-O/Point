import React, { useContext, useEffect, useState } from 'react';
import {
	Navbar,
	NavbarBrand,
	NavbarMenuToggle,
	NavbarMenuItem,
	NavbarMenu,
	NavbarContent,
	NavbarItem,
	Link,
	Button,
} from '@nextui-org/react';
import { FaInstagram, FaFacebook, FaYoutube } from 'react-icons/fa';
import { Image } from '@nextui-org/react';
import Logo from '../../../public/images/logo_point_wite.png';
import LangContext from '@/components/langContext/LangContext.jsx';
import { color } from 'framer-motion';
import { useLocation } from 'react-router-dom';

export default function NavBarPages() {
	const [isMenuOpen, setIsMenuOpen] = React.useState(false);
	const { lang, toggleLang } = React.useContext(LangContext);
	const location = useLocation();
	const menuItems = [
		{ name: lang === 'en' ? 'News' : 'الأخبار', link: '/news' },
		{ name: lang === 'en' ? 'Speakers' : 'المتحدثين', link: '/speakers' },
		{ name: lang === 'en' ? 'Agenda' : 'جدول الاعمال', link: '/programs' },
		{ name: lang === 'en' ? 'Conference' : 'المؤتمر', link: '/conference' },
		{ name: lang === 'en' ? 'Stream' : 'مقاطع الفيديو', link: '/stream' },
	];
	useEffect(() => {
		document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
	}, [lang]);

	return (
		<Navbar
			isBordered
			isMenuOpen={isMenuOpen}
			onMenuOpenChange={setIsMenuOpen}
			className='bg-black text-white'
			style={{ height: '9%' }}
		>
			{/* Hamburger Menu for Small Screens */}
			<NavbarContent className='sm:hidden bg-black' justify='between'>
				{lang === 'en' ? (
					<a href='/'>
						<Image
							src={Logo}
							alt='logo'
							className='min-w-28 max-w-28  px-1  logo_img_en '
						/>
					</a>
				) : (
					<a href='/'>
						<Image
							src={Logo}
							alt='logo'
							className='min-w-28 max-w-28 px-1 logo_img_en '
						/>
					</a>
				)}
				{/* <p className='font-bold text-2xl'>POINT</p> */}
				<NavbarMenuToggle
					aria-label={isMenuOpen ? 'Close menu' : 'Open menu'}
				/>
			</NavbarContent>

			{/* Logo and Brand */}
			<NavbarContent className='sm:hidden pr-3 ' justify='center'>
				<NavbarBrand>
					<Link href='/'>
						<Image src={Logo} alt='logo' className='w-12 ' />
					</Link>
					{/* <p className='font-bold text-inherit'>POINT</p> */}
				</NavbarBrand>
			</NavbarContent>

			{/* Navbar Content for Larger Screens */}
			<NavbarContent
				className='hidden sm:flex gap-4 text-green'
				justify='center'
			>
				<NavbarBrand className='px-4'>
					{lang === 'en' ? (
						<Link href='/'>
							<Image
								src={Logo}
								alt='logo'
								className='min-w-28 max-w-28 px-2  logo_img_en '
							/>
						</Link>
					) : (
						<Link href='/'>
							<Image
								src={Logo}
								alt='logo'
								className='min-w-28 max-w-28 px-2 logo_img_en '
							/>
						</Link>
					)}
					{/* <p className='font-bold text-2xl'>POINT</p> */}
				</NavbarBrand>
				{menuItems.map((item, index) => (
					<NavbarItem key={index}>
						<Link
							className={`text-white text-xl px-2 ${
								location.pathname === item.link ? 'text-lime-500 font-bold' : ''
							}`}
							href={item.link}
						>
							{item.name}
						</Link>
					</NavbarItem>
				))}
			</NavbarContent>

			{/* Social Icons and Language Switch Button */}

			<NavbarContent justify='end'>
				<div className='flex gap-4'>
					<a
						href='https://www.instagram.com/tawasoul.ngo?igsh=NWZnM3A3cDJqbTRo'
						target='_blank'
						rel='noopener noreferrer'
					>
						<FaInstagram className='text-rose-500 text-2xl' />
					</a>
					<a
						href='https://web.facebook.com/Tawasoulorganization/about'
						target='_blank'
						rel='noopener noreferrer'
					>
						<FaFacebook className='text-blue-500 text-2xl' />
					</a>
					<a
						href='https://www.instagram.com/tawasoul.ngo?igsh=NWZnM3A3cDJqbTRo'
						target='_blank'
						rel='noopener noreferrer'
					>
						<FaYoutube className='text-red-500 text-2xl' />
					</a>
				</div>
				<Button
					auto
					className='ml-4 text-white  bg-lime-500'
					onClick={toggleLang}
				>
					{lang === 'en' ? 'العربية' : 'English'}
				</Button>
			</NavbarContent>

			{/* Menu for Small Screens */}
			<NavbarMenu>
				{menuItems.map((item, index) => (
					<NavbarMenuItem key={`${item.name}-${index}`} className=''>
						<Link
							className={`w-full text-black ${
								location.pathname === item.link ? 'text-lime-500 font-bold' : ''
							}`}
							href={item.link}
							size='lg'
						>
							{item.name}
						</Link>
					</NavbarMenuItem>
				))}
			</NavbarMenu>
		</Navbar>
	);
}
