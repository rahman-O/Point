import React, { useEffect, useState } from 'react';
import LangContext from './LangContext.jsx';

const LangProvider = ({ children }) => {
	const [lang, setLang] = useState('ar');
	useEffect(() => {
		const storedLang = localStorage.getItem('lang');
		if (storedLang) {
			setLang(storedLang);
		}
	}, []);
	const toggleLang = () => {
		const newLang = lang === 'en' ? 'ar' : 'en';
		setLang(newLang);
		localStorage.setItem('lang', newLang);
	};
	return (
		<LangContext.Provider value={{ lang, toggleLang }}>
			{children}
		</LangContext.Provider>
	);
};

export default LangProvider;
