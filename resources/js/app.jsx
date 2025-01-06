// resources/js/app.js
import { createRoot } from 'react-dom/client';
import { createBrowserRouter, RouterProvider } from 'react-router-dom';
import axios from 'axios';

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

// Import Contexts and Loader
import LangProvider from '@/components/langContext/LangProvider.jsx';
import {
	LoaderProvider,
	useLoader,
} from '@/components/loaderContext/LoaderContext.jsx';
import Loader from '@/components/Loader.jsx';

// Set up Axios interceptors
function setupAxiosInterceptors(showLoader, hideLoader) {
	axios.interceptors.request.use(
		(config) => {
			showLoader();
			return config;
		},
		(error) => {
			hideLoader();
			return Promise.reject(error);
		}
	);

	axios.interceptors.response.use(
		(response) => {
			hideLoader();
			return response;
		},
		(error) => {
			hideLoader();
			return Promise.reject(error);
		}
	);
}

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

function App() {
	const { isLoading, showLoader, hideLoader } = useLoader();

	// Set up Axios interceptors
	setupAxiosInterceptors(showLoader, hideLoader);

	return (
		<>
			{isLoading && <Loader />}
			<RouterProvider router={router} />
		</>
	);
}

createRoot(document.getElementById('root')).render(
	<LangProvider>
		<LoaderProvider>
			<App />
		</LoaderProvider>
	</LangProvider>
);
