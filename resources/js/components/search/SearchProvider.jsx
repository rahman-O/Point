import React, { createContext, useCallback, useContext, useEffect, useMemo, useRef, useState } from 'react';
import { useLocation } from 'react-router-dom';
import SearchDialog from './SearchDialog.jsx';

const SearchContext = createContext({ open: () => {}, close: () => {} });

const isEditable = (el) => el instanceof HTMLElement && (el.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(el.tagName));

export function SearchProvider({ children }) {
	const [state, setState] = useState({ isOpen: false, query: undefined, type: null });
	const returnFocusRef = useRef(null);
	const location = useLocation();

	const open = useCallback((options = {}) => {
		returnFocusRef.current = document.activeElement;
		setState({ isOpen: true, query: options.query, type: options.type ?? null });
	}, []);
	const close = useCallback(() => setState((prev) => ({ ...prev, isOpen: false })), []);

	useEffect(() => {
		if (state.isOpen || !(returnFocusRef.current instanceof HTMLElement)) return;
		if (returnFocusRef.current.isConnected) returnFocusRef.current.focus({ preventScroll: true });
		returnFocusRef.current = null;
	}, [state.isOpen]);

	useEffect(() => {
		const onKeyDown = (event) => {
			const isShortcut = (event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k';
			const isSlash = event.key === '/' && !isEditable(event.target);
			if (isShortcut || (isSlash && !state.isOpen)) {
				event.preventDefault();
				isShortcut && state.isOpen ? close() : open();
			}
		};
		window.addEventListener('keydown', onKeyDown);
		return () => window.removeEventListener('keydown', onKeyDown);
	}, [state.isOpen, open, close]);

	useEffect(close, [location.pathname, location.search, close]);

	const value = useMemo(() => ({ open, close, isOpen: state.isOpen }), [open, close, state.isOpen]);

	return (
		<SearchContext.Provider value={value}>
			{children}
			<SearchDialog isOpen={state.isOpen} initialQuery={state.query} initialType={state.type} onClose={close} />
		</SearchContext.Provider>
	);
}

export const useSearchDialog = () => useContext(SearchContext);
