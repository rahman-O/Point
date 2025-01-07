import React, { useEffect, useState } from 'react';
import LangContext from './LangContext.jsx';

const LangProvider = ({ children }) => {
	const [lang, setLang] = useState('ar');

	useEffect(() => {
		const storedLang = localStorage.getItem('lang');
		if (storedLang) {
			setLang(storedLang);
		}

		// Set the initial body class
		document.body.className = storedLang === 'en' ? 'font-en' : 'font-ar';
	}, []);

	const toggleLang = () => {
		const newLang = lang === 'en' ? 'ar' : 'en';
		setLang(newLang);
		localStorage.setItem('lang', newLang);

		// Dynamically update the body class
		document.body.className = newLang === 'en' ? 'font-en' : 'font-ar';
	};

	return (
		<LangContext.Provider value={{ lang, toggleLang }}>
			{children}
		</LangContext.Provider>
	);
};

export default LangProvider;
