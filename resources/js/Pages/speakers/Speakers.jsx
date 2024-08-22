
import React, {useEffect, useState} from "react";
import NavBarPages from "../../components/NavBarPages.jsx";
import axios from "axios";
import { CardSpeaker} from "@/components/CardSpeaker.jsx";

export default function Speakers() {
const [speakers, setSpeakers] = useState([]);
const data =async ()=> {
    const speakers = await axios.get('/api/speakers');
    if (speakers) {

        setSpeakers(speakers.data);
        console.log(speakers.data);

    }
}
useEffect(() => {
    data();
} , [


]);
    return (
        <div>

            <h1>{speakers.map(
                (speaker) => {
                    return <div key={speaker.id} className={"grid grid-cols-3  gap-4 pt-10 container"}>
                        <CardSpeaker name={speaker.name_en} conutary={speaker.country_en} image={speaker.image} dis={speaker.image}/>
                        <CardSpeaker name={speaker.name_en} conutary={speaker.country_en} image={speaker.image} dis={speaker.image}/>
                        <CardSpeaker name={speaker.name_en} conutary={speaker.country_en} image={speaker.image} dis={speaker.image}/>
                        <CardSpeaker name={speaker.name_en} conutary={speaker.country_en} image={speaker.image} dis={speaker.image}/>
                        <CardSpeaker name={speaker.name_en} conutary={speaker.country_en} image={speaker.image} dis={speaker.image}/>
                        <CardSpeaker name={speaker.name_en} conutary={speaker.country_en} image={speaker.image} dis={speaker.image}/>
                    </div>
                }
            )}</h1>
        </div>
    );
}
