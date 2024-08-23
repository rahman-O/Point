import React, {useEffect, useState} from "react";
import axios from "axios";
import {CardNews} from "@/Components/CardNews.jsx";
import Pagination from "@/Components/Pagination.jsx";

export default function News() {
    const [news, setNews] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);

    const fetchData = async (page) => {
        const response = await axios.get(`/api/news?page=${page}`);
        if (response) {
            setNews(response.data.data);
            setTotalPages(response.data.last_page);
        }
    };

    useEffect(() => {
        fetchData(currentPage);
    }, [currentPage]);

    const handleNextPage = () => {
        if (currentPage < totalPages) {
            setCurrentPage(currentPage + 1);
        }
    };

    const handlePrevPage = () => {
        if (currentPage > 1) {
            setCurrentPage(currentPage - 1);
        }
    };
    const handlePageChange = (page) => {
        setCurrentPage(page);
    };
    return (
        <div className="py-6">
            <div className="grid grid-cols-1 md:grid-cols-6 sm:grid-cols-3 gap-1 px-6 py-4">
                {news.map((post) => (
                    <CardNews
                        key={post.id}
                        desc_en={post.desc_en}
                        event_time={post.event_time}
                        image={post.image}
                    />
                ))}
            </div>

            <Pagination
                currentPage={currentPage}
                totalPages={totalPages}
                onPageChange={handlePageChange}
            />
        </div>
    );
}

