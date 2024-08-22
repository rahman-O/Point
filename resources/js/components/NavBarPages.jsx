import React from 'react';
import ReactDOM from 'react-dom/client';
import {Navbar, NavbarBrand, NavbarContent, NavbarItem,} from "@nextui-org/navbar";
import {Button} from "@nextui-org/button";
import {Link} from "@nextui-org/link";



function logIn() {
    console.log("Logging in....");
}
function NavBarPages() {
    let [isActivePage, setIsActivePage] = React.useState("News");

    function changeActivePage(page) {
        setIsActivePage(page);
    }

    return (
        <Navbar>
            <NavbarBrand>
            {/*  //  <AcmeLogo />*/}
                <p className="font-bold text-inherit">Point</p>
            </NavbarBrand>


            <NavbarContent className="hidden sm:flex gap-4" justify="center">

                <NavbarItem isActive={isActivePage==="News"}>
                    <Link  onClick={changeActivePage("News")} color={ isActivePage==="News"? "primary" : "foreground"}  href="#" aria-current="page">
                        News

                    </Link>
                </NavbarItem>


                <NavbarItem isActive={isActivePage==="Speakers"}  >
                    <Link onClick={changeActivePage("Speakers")}  color={ isActivePage==="Speakers"? "primary" : "foreground"} href="#" aria-current="page">
                        Speakers
                    </Link>
                </NavbarItem>














                <NavbarItem>
                    <Link color="foreground" href="#">
                        Programs
                    </Link>
                </NavbarItem>
                <NavbarItem>
                    <Link color="foreground" href="#">
                        Stream
                    </Link>
                </NavbarItem>
                <NavbarItem>
                    <Link color="foreground" href="#">
                        Performance
                    </Link>
                </NavbarItem>
                <NavbarItem>
                    <Link color="foreground" href="#">
                        Voting
                    </Link>
                </NavbarItem>
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

