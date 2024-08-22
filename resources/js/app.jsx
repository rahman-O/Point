import {createRoot} from "react-dom/client";
import {createBrowserRouter ,RouterProvider} from 'react-router-dom';

//import css
import '../css/app.css';

import NavBarPages from './components/NavBarPages.jsx';
import HomePage from "./Pages/HomePage.jsx";
import HandelPages from "./components/HandelPages.jsx";
import Speakers from "./Pages/Speakers.jsx";

const routes = [
    {
        path: '/',
        element: <HandelPages/>
    },
    {
        path: "/speakers",
        element: <Speakers/>
    }
    ]


createRoot(document.getElementById('root')).render(

<RouterProvider  router={createBrowserRouter(routes)}>
</RouterProvider>

);
