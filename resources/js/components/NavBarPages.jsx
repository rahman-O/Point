import React, {useContext, useEffect, useState} from 'react';
import {NavLink, useLocation} from 'react-router-dom';
import {Navbar, NavbarBrand, NavbarContent, NavbarItem} from "@nextui-org/navbar";
import {Image} from "@nextui-org/react";
import Logo from "../../../public/images/logoPoint.png";
import {FaBars, FaFacebook, FaTimes, FaTwitter, FaYoutube} from "react-icons/fa";
import {Button} from "flowbite-react";
import LangContext from "@/components/langContext/LangContext.jsx";

function NavBarPages() {
    const location = useLocation();
    const {lang, toggleLang} = useContext(LangContext);

    // State to toggle menu visibility on small screens
    const [isMenuOpen, setIsMenuOpen] = useState(false);

    // Change the text direction based on the selected language
    useEffect(() => {
        document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
    }, [lang]);

    // Toggle the menu on hamburger icon click
    const toggleMenu = () => {
        setIsMenuOpen(!isMenuOpen);
    };

    const NavItem = ({pageName, href, onClick}) => {
        const isActive = location.pathname === href;
        return (
            <NavbarItem isActive={isActive}>
                <NavLink
                    to={href}
                    onClick={onClick}
                    className={`transition duration-300 ease-in-out transform ${
                        isActive ? "text-white" : "hover:text-white text-lime-500"
                    }`}
                    aria-current={isActive ? "page" : undefined}
                >
                    {pageName}
                </NavLink>
            </NavbarItem>
        );
    };

    return (
        <Navbar
            className={`bg-black ${isMenuOpen ? 'h-full w-full' : 'w-full'}`}
        >
            <NavbarBrand>
                <Image
                    alt="logo"
                    className="w-12"
                    src={Logo}
                />
                <p className="font-bold text-2xl text-white px-2">POINT</p>
            </NavbarBrand>

            {/* Hamburger menu icon for small screens */}
            <div className="sm:hidden fixed top-4 z-50 px-4">
                <button onClick={toggleMenu} className="text-white">
                    {/* Toggling between FaBars and FaTimes */}
                    {isMenuOpen ? <FaTimes className="text-3xl"/> : <FaBars className="text-3xl"/>}
                </button>
            </div>

            {/* Full-screen overlay menu for small screens */}
            {isMenuOpen && (
                <div
                    className="fixed h-full w-full bg-gradient-to-r from-blue-900 via-teal-800 to-teal-500 z-40 grid justify-items-center gap-4 pt-4">
                    <NavItem pageName={lang === 'en' ? 'Home' : 'الصفحة الرئيسية'} href="/" onClick={toggleMenu}/>
                    <NavItem pageName={lang === 'en' ? 'News' : 'الأخبار'} href="/news" onClick={toggleMenu}/>
                    <NavItem pageName={lang === 'en' ? 'Speakers' : 'الاجندة'} href="/speakers" onClick={toggleMenu}/>
                    <NavItem pageName={lang === 'en' ? 'Programs' : 'البرامج'} href="/programs" onClick={toggleMenu}/>
                    <NavItem pageName={lang === 'en' ? 'Conference' : 'المؤتمر'} href="/conference"
                             onClick={toggleMenu}/>
                    <NavItem pageName={lang === 'en' ? 'Stream' : 'البث'} href="/stream" onClick={toggleMenu}/>

                    <div className="flex gap-4 mt-8">
                        <FaTwitter className="text-cyan-500 text-2xl" onClick={toggleMenu}/>
                        <FaFacebook className="text-blue-500 text-2xl" onClick={toggleMenu}/>
                        <FaYoutube className="text-red-500 text-2xl" onClick={toggleMenu}/>
                    </div>

                    <Button
                        auto
                        color="secondary"
                        className="text-white mt-4"
                        onClick={toggleLang}
                    >
                        {lang === 'en' ? 'العربية' : 'English'}
                    </Button>
                </div>
            )}

            {/* Regular Navbar for larger screens */}
            <NavbarContent className="hidden sm:flex" justify="center">
                <NavItem pageName={lang === 'en' ? 'Home' : 'الصفحة الرئيسية'} href="/"/>
                <NavItem pageName={lang === 'en' ? 'News' : 'الأخبار'} href="/news"/>
                <NavItem pageName={lang === 'en' ? 'Speakers' : 'الاجندة'} href="/speakers"/>
                <NavItem pageName={lang === 'en' ? 'Programs' : 'البرامج'} href="/programs"/>
                <NavItem pageName={lang === 'en' ? 'Conference' : 'المؤتمر'} href="/conference"/>
                <NavItem pageName={lang === 'en' ? 'Stream' : 'البث'} href="/stream"/>
            </NavbarContent>

            <NavbarContent justify="end" className="hidden sm:flex">
                <NavbarItem className="flex gap-2">
                    <FaTwitter className="text-cyan-500 text-2xl"/>
                    <FaFacebook className="text-blue-500 text-2xl"/>
                    <FaYoutube className="text-red-500 text-2xl"/>
                </NavbarItem>

                <NavbarItem>
                    <Button
                        auto
                        color="secondary"
                        className="text-white ml-4"
                        onClick={toggleLang}
                    >
                        {lang === 'en' ? 'العربية' : 'English'}
                    </Button>
                </NavbarItem>
            </NavbarContent>
        </Navbar>
    );
}

export default NavBarPages;
