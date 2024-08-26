import {Outlet} from "react-router-dom";
import NavBarPages from "@/components/NavBarPages.jsx";

export const Layout = ({}) => {
    return (
        <>
            <NavBarPages/>
            <div>
                <Outlet/>
            </div>
        </>
    );
}
