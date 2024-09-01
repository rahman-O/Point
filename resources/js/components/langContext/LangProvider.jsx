import React, { useState } from 'react';
import LangContext from './LangContext.jsx';

const LangProvider = ({ children }) => {
    const [lang, setLang] = useState('ar');

    const toggleLang = () => {
        setLang(lang === 'en' ? 'ar' : 'en');
    };

    return (
        <LangContext.Provider value={{ lang, toggleLang }}>
            {children}
        </LangContext.Provider>
    );
};

export default LangProvider;
