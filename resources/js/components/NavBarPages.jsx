import React from 'react';
import {NavLink, useLocation} from 'react-router-dom';
import {Navbar, NavbarBrand, NavbarContent, NavbarItem} from "@nextui-org/navbar";
import {Image} from "@nextui-org/react";
import Logo from "../../../public/images/logoPoint.png"

function NavBarPages() {
    const location = useLocation();

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
                <p className="font-bold text-2xl text-white px-3">POINT</p>
            </NavbarBrand>

            <NavbarContent className="hidden sm:flex gap-8 	" justify="center">
                <NavItem pageName="Home" href="/"/>
                <NavItem pageName="News" href="/news"/>
                <NavItem pageName="Speakers" href="/speakers"/>
                <NavItem pageName="Programs" href="/programs"/>
                <NavItem pageName="Stream" href="/stream"/>
                <NavItem pageName="Performance" href="/performance"/>
            </NavbarContent>

            {/*<NavbarContent justify="end">*/}
            {/*    <NavbarItem>*/}
            {/*        <Button color="primary" href="#" variant="flat">*/}
            {/*            Register*/}
            {/*        </Button>*/}
            {/*    </NavbarItem>*/}
            {/*</NavbarContent>*/}
        </Navbar>
    );
}

export default NavBarPages;
