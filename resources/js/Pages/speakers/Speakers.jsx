import React, {useEffect, useState} from "react";
import axios from "axios";
import {CardSpeaker} from "@/components/CardSpeaker.jsx";
import Pagination from "@/components/Pagination.jsx";

export default function Speakers() {
    const [speakers, setSpeakers] = useState([]);
    const [currentPage, setCurrentPage] = useState(1); // State to track the current page
    const [totalPages, setTotalPages] = useState(1); // State to track the total number of pages

    const fetchData = async (page) => {
        const response = await axios.get(`/api/speakers?page=${page}`);
        if (response) {
            setSpeakers(response.data.data); // Data is in `data` key when using pagination
            setTotalPages(response.data.last_page); // Set the total number of pages
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
            <div className="grid grid-cols-1 md:grid-cols-6 sm:grid-cols-3 gap-2 px-6 py-4 ">
                {speakers.map((speaker) => (
                   
                    <CardSpeaker
                        key={speaker.id}
                        name={speaker.name_en}
                        job_en={speaker.job_en}
                        image={speaker.image}
                        conutry={speaker.country_en}
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
