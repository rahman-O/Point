import React from "react";
import NewsSlider from "@/Pages/homePage/NewsSlider.jsx";
import SpeackerSlider from "@/Pages/homePage/SpeackerSlider.jsx";
import videoPlayes from "../../../css/images/POINT12-24-25.mp4";

export default function HomePage() {
    return (
        <div className="">
            <video className="banner" muted autoPlay loop src={videoPlayes}></video>
            <div className="grid gap-6">
                <NewsSlider/>
                <h1 className="text-2xl font-bold px-2 text-center uppercase">Speakers</h1>
                <SpeackerSlider/>
            </div>
        </div>
    );
}
