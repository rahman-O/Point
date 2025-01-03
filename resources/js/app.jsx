// resources/js/app.js
import { createRoot } from 'react-dom/client';
import { createBrowserRouter, RouterProvider } from 'react-router-dom';

// Import CSS
import '../css/app.css';

// Import Layout and Page Components
import News from './Pages/news/News.jsx';
import NewsDetails from './Pages/news/NewsDetails.jsx';
import SpeakersDetails from './Pages/speakers/SpeakersDetails.jsx';
import Speakers from './Pages/speakers/Speakers.jsx';
import Programs from './Pages/programs/Programs.jsx';
import Stream from './Pages/stream/Stream.jsx';
import Voting from '@/Pages/voting/Voting.jsx';
import { Layout } from '@/layout/Layout.jsx';
import HomePage from '@/Pages/homePage/HomePage.jsx';
import NotFoundPage from '@/Pages/NotFoundPage';

import Conferences from '@/Pages/conferences/Conferences.jsx';
import LangProvider from '@/components/langContext/LangProvider.jsx';

const routes = [
	{
		path: '/',
		element: <Layout />,
		children: [
			{ path: '', element: <HomePage /> },
			{ path: 'news', element: <News /> },
			{ path: 'news/:id', element: <NewsDetails /> },
			{ path: 'speakers', element: <Speakers /> },
			{ path: 'speakers/:id', element: <SpeakersDetails /> },
			{ path: 'programs', element: <Programs /> },
			{ path: 'stream', element: <Stream /> },
			{ path: 'conference', element: <Conferences /> },
			{ path: 'voting', element: <Voting /> },
			{ path: '*', element: <NotFoundPage /> },
		],
	},
];

const router = createBrowserRouter(routes);

createRoot(document.getElementById('root')).render(
	<LangProvider>
		<RouterProvider router={router} />
	</LangProvider>
);
