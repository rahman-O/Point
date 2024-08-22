import React from 'react';
import {NavLink, useLocation} from 'react-router-dom';
import {Navbar, NavbarBrand, NavbarContent, NavbarItem} from "@nextui-org/navbar";
import {Button} from "@nextui-org/button";

function NavBarPages() {
    const location = useLocation();

    const NavItem = ({pageName, href}) => {
        const isActive = location.pathname === href;

        return (
            <NavbarItem isActive={isActive}>
                <NavLink
                    to={href}
                    className={`transition duration-300 ease-in-out transform ${
                        isActive ? "scale-105 text-blue-500 font-bold" : "hover:scale-105 hover:text-blue-500"
                    }`}
                    aria-current={isActive ? "page" : undefined}
                >
                    {pageName}
                </NavLink>
            </NavbarItem>
        );
    };

    return (
        <Navbar>
            <NavbarBrand>
                <p className="font-bold text-inherit">Point</p>
            </NavbarBrand>

            <NavbarContent className="hidden sm:flex gap-4" justify="center">
                <NavItem pageName="Home" href="/"/>
                <NavItem pageName="News" href="/news"/>
                <NavItem pageName="Speakers" href="/speakers"/>
                <NavItem pageName="Programs" href="/programs"/>
                <NavItem pageName="Stream" href="/stream"/>
                <NavItem pageName="Performance" href="/performance"/>
            </NavbarContent>

            <NavbarContent justify="end">
                <NavbarItem>
                    <Button color="primary" href="#" variant="flat">
                        Register
                    </Button>
                </NavbarItem>
            </NavbarContent>
        </Navbar>
    );
}

export default NavBarPages;
