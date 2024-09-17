import React from "react";
import NavBarPages from "./NavBarPages.jsx";
import HomePage from "@/Pages/homePage/HomePage.jsx";
import FooterPages from "@/Components/FooterPages.jsx";


export default function HandelPages() {
    return (

        <div>
            <NavBarPages/>
            <HomePage/>
            <FooterPages/>

        </div>
    );
}
