// resources/js/app.js
import { createRoot } from "react-dom/client";
import { createBrowserRouter, RouterProvider } from 'react-router-dom';

// Import CSS
import '../css/app.css';

// Import Layout and Page Components
import News from "./Pages/news/News.jsx";
import Speakers from "./Pages/speakers/Speakers.jsx";
import Programs from "./Pages/programs/Programs.jsx";
import Stream from "./Pages/stream/Stream.jsx";
import Performance from "./Pages/performance/Performance.jsx";
import Voting from "@/Pages/voting/Voting.jsx";
import {Layout} from "@/layout/Layout.jsx";

const routes = [
    {
        path: '/',
        element: <Layout />,
        children: [
            { path: 'news', element: <News /> },
            { path: 'speakers', element: <Speakers /> },
            { path: 'programs', element: <Programs /> },
            { path: 'stream', element: <Stream /> },
            { path: 'performance', element: <Performance /> },
            { path: 'voting', element: <Voting /> },
        ],
    },
];

const router = createBrowserRouter(routes);

createRoot(document.getElementById('root')).render(
    <RouterProvider router={router} />
);
