import React, {useContext, useEffect, useState} from "react";
import Slider from "react-slick";
import axios from "axios";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import {CgWorkAlt} from "react-icons/cg";
import LangContext from "@/components/langContext/LangContext.jsx";

export default function NewsSlider() {
    const [speakers, setSpeakers] = useState([]);
    const { lang, toggleLang} = useContext(LangContext);
    useEffect(() => {
        // Fetch all speakers without pagination
        axios.get('/api/all/speakers').then(response => {
            setSpeakers(response.data);
        });
    }, []);

    const settings = {
        dots: true,
        infinite: true,
        speed: 500,
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
    };

    return (
        <div className="h-fit overflow-hidden">
            <Slider {...settings}>
                {speakers.map((speaker) => (
                    <div key={speaker.id} className="px-4 py-0">
                        <div className="border rounded-lg shadow-lg overflow-hidden">
                            <img
                                className="w-full h-48 object-cover"
                                src={`/api/images/${speaker.image}`}
                                alt="Speaker Image"
                            />
                            <div className="p-4">
                                <div className="flex justify-between items-center">
                                    <h2 className="text-lg font-bold mb-2">{lang==='en'?speaker.name_en:speaker.name_ar}</h2>
                                    <span className="text-right">
                                        <CgWorkAlt className="inline pr-2 text-3xl"/>
                                        { lang==='en'?speaker.job_en:speaker.job_ar}
                                    </span>
                                </div>
                                <p className="text-sm text-gray-600">{lang==='en'?  speaker.country_en:speaker.country_ar}</p>
                            </div>
                        </div>
                    </div>
                ))}
            </Slider>
        </div>
    );
}
