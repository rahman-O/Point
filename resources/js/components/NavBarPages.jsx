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
import Logo from '../../../public/images/logo_point_black_on_white.jpeg';
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
			className='bg-white text-black w-full border-b border-gray-200'
			style={{ height: '9%' }}
		>
			{/* Hamburger Menu for Small Screens */}
			<NavbarContent
				className='lg:hidden bg-white w-full px-4 flex justify-between'
				justify='between'
			>
				<div className=''>
					{lang === 'en' ? (
						<a href='/'>
							<Image
								src={Logo}
								alt='logo'
								className='min-w-32 max-w-32   logo_img_en '
							/>
						</a>
					) : (
						<a href='/'>
							<Image
								src={Logo}
								alt='logo'
								className='min-w-32 max-w-32  first-letter: logo_img_en '
							/>
						</a>
					)}
				</div>
				<NavbarMenuToggle
					aria-label={isMenuOpen ? 'Close menu' : 'Open menu'}
				/>
			</NavbarContent>
			{/* Navbar Content for Larger Screens */}
			<NavbarContent
				className='hidden lg:flex gap-4 text-green'
				justify='center'
			>
				<NavbarBrand className='px-4'>
					{lang === 'en' ? (
						<Link href='/'>
							<Image
								src={Logo}
								alt='logo'
								className='min-w-36 max-w-36 px-2  logo_img_en '
							/>
						</Link>
					) : (
						<Link href='/'>
							<Image
								src={Logo}
								alt='logo'
								className='min-w-36 max-w-36 px-2  logo_img_en '
							/>
						</Link>
					)}
					{/* <p className='font-bold text-2xl'>POINT</p> */}
				</NavbarBrand>
				{menuItems.map((item, index) => (
					<NavbarItem key={index}>
						<Link
							className={`text-black text-xl px-2 hover:text-point-crimson transition-colors ${
								location.pathname === item.link
									? 'text-point-crimson font-bold'
									: ''
							}`}
							href={item.link}
						>
							{item.name}
						</Link>
					</NavbarItem>
				))}
			</NavbarContent>
			{/* Social Icons and Language Switch Button */}

			<NavbarContent className='hidden lg:flex gap-4 text-green' justify='end'>
				<div className='flex gap-3'>
					<a
						href='https://www.instagram.com/point_iq?igsh=MTJ5N242ZTlyb3ppag=='
						target='_blank'
						rel='noopener noreferrer'
						className='flex items-center justify-center w-9 h-9 rounded-full bg-point-purple text-white transition-opacity hover:opacity-80'
					>
						<FaInstagram className='text-lg' />
					</a>
					<a
						href='https://www.facebook.com/share/1DTsbaixU8/'
						target='_blank'
						rel='noopener noreferrer'
						className='flex items-center justify-center w-9 h-9 rounded-full bg-point-purple text-white transition-opacity hover:opacity-80'
					>
						<FaFacebook className='text-lg' />
					</a>
					<a
						href='https://www.youtube.com/@pointmenaconference4041'
						target='_blank'
						rel='noopener noreferrer'
						className='flex items-center justify-center w-9 h-9 rounded-full bg-point-purple text-white transition-opacity hover:opacity-80'
					>
						<FaYoutube className='text-lg' />
					</a>
				</div>
				<Button
					auto
					className='ml-4 text-white bg-point-purple transition-colors border border-point-purple font-bold'
					onClick={toggleLang}
				>
					{lang === 'en' ? 'العربية' : 'English'}
				</Button>
			</NavbarContent>

			{/* Menu for Small Screens */}
			<NavbarMenu>
				{menuItems.map((item, index) => (
					<NavbarMenuItem key={`${item.name}-${index}`}>
						<Link
							className={`w-full text-black ${
								location.pathname === item.link
									? 'text-point-crimson font-bold'
									: ''
							}`}
							href={item.link}
							size='lg'
						>
							{item.name}
						</Link>
					</NavbarMenuItem>
				))}
				<div className='flex gap-4 items-center justify-center mt-4'>
					<a
						href='https://www.instagram.com/point_iq?igsh=MTJ5N242ZTlyb3ppag=='
						target='_blank'
						rel='noopener noreferrer'
						className='flex items-center justify-center w-9 h-9 rounded-full bg-point-purple text-white transition-opacity hover:opacity-80'
					>
						<FaInstagram className='text-lg' />
					</a>
					<a
						href='https://www.facebook.com/share/1DTsbaixU8/'
						target='_blank'
						rel='noopener noreferrer'
						className='flex items-center justify-center w-9 h-9 rounded-full bg-point-purple text-white transition-opacity hover:opacity-80'
					>
						<FaFacebook className='text-lg' />
					</a>
					<a
						href='https://www.youtube.com/@pointmenaconference4041'
						target='_blank'
						rel='noopener noreferrer'
						className='flex items-center justify-center w-9 h-9 rounded-full bg-point-purple text-white transition-opacity hover:opacity-80'
					>
						<FaYoutube className='text-lg' />
					</a>
					<button
						auto
						className='text-white bg-point-purple transition-colors rounded-lg py-1 px-2'
						onClick={toggleLang}
					>
						{lang === 'en' ? 'AR' : 'EN'}
					</button>
				</div>
			</NavbarMenu>
		</Navbar>
	);
}
