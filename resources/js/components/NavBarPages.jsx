import React from 'react';
import ReactDOM from 'react-dom/client';
import {Navbar, NavbarBrand, NavbarContent, NavbarItem,} from "@nextui-org/navbar";
import {Button} from "@nextui-org/button";
import {Link} from "@nextui-org/link";
import {NavLink} from "react-router-dom";




function NavBarPages() {

    let [isActivePage, setIsActivePage] = React.useState("News");

    function changeActivePage(page) {
        setIsActivePage(page);
    }

    const NavItem =  ({ pageName, href })  => (
        <NavbarItem isActive={isActivePage === pageName}>
            <NavLink
                onClick={() => changeActivePage(pageName)}
                color={isActivePage === pageName ? "primary" : "foreground"}

                aria-current={isActivePage === pageName ? "page" : undefined}
                className={`transition duration-300 ease-in-out transform ${
                    isActivePage === pageName ? "scale-105 text-primary" : "hover:scale-105 hover:text-primary"
                }`}
             to={href}>
                {pageName}
            </NavLink>
        </NavbarItem>
    );

    return (
        <Navbar>
            <NavbarBrand>
                <p className="font-bold text-inherit">Point</p>
            </NavbarBrand>

            <NavbarContent className="hidden sm:flex gap-4" justify="center">
                <NavItem pageName="News" href="/news"/>
                <NavItem pageName="Speakers" href="/speakers"/>
                <NavItem pageName="Programs" href="/programs"/>
                <NavItem pageName="Stream" href="/stream"/>
                <NavItem pageName="Performance" href="/performance"/>
                <NavItem pageName="Voting" href="/voting"/>
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

