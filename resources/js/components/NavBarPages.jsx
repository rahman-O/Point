import React, {useContext} from 'react';
import {NavLink, useLocation} from 'react-router-dom';
import {Navbar, NavbarBrand, NavbarContent, NavbarItem} from "@nextui-org/navbar";
import {Image} from "@nextui-org/react";
import Logo from "../../../public/images/logoPoint.png"
import {FaFacebook, FaTwitter, FaYoutube} from "react-icons/fa";
import {Button} from "flowbite-react";
import LangContext from "@/components/langContext/LangContext.jsx";

function NavBarPages() {
    const location = useLocation();
    const { lang, toggleLang} = useContext(LangContext);
    const NavItem = ({pageName, href}) => {
        const isActive = location.pathname === href;
        return (
            <NavbarItem isActive={isActive}>
                <NavLink
                    to={href}
                    className={`transition duration-300 ease-in-out transform ${
                        isActive ? " text-white" : "hover:text-white text-lime-500"
                    }`}
                    aria-current={isActive ? "page" : undefined}
                >
                    {pageName}
                </NavLink>
            </NavbarItem>
        );
    };

    return (
        <Navbar className="bg-black">
            <NavbarBrand>
                <Image
                    alt="logo"
                    className="w-12"
                    src={Logo}
                />
                <p className="font-bold text-2xl text-white ">POINT</p>
            </NavbarBrand>

            <NavbarContent className="hidden sm:flex  	" justify="center">
                <NavItem pageName={lang === 'en' ? 'Home' : 'الصفحة الرئيسية'} href="/" />
                <NavItem pageName={lang === 'en' ? 'News' : 'الأخبار'} href="/news" />
                <NavItem pageName={lang === 'en' ? 'Speakers' : 'الاجندة'} href="/speakers" />
                <NavItem pageName={lang === 'en' ? 'Programs' : 'البرامج'} href="/programs" />
                <NavItem pageName={lang === 'en' ? 'Conference' : 'المؤتمر'} href="/conference" />
                <NavItem pageName={lang === 'en' ? 'Stream' : 'البث'} href="/stream" />
            </NavbarContent>

            <NavbarContent justify="end">
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
                         // Handle click event to toggle language
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
