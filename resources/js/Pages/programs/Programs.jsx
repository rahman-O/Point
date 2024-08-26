import React, {useEffect, useState} from "react";
import {TapsSessions} from "./componets/TapsSessions";
import axios from "axios";
import {SpeakerList} from "@/Pages/programs/componets/SpeakerList.jsx";


export default function Programs() {

    const [porgrams, setProgram] = React.useState([]);


    const [activeTab, setActiveTab] = useState(1);

    useEffect(() => {

        axios.get('/api/programs')
            .then(response => {
                console.log(response.data.data);
                setProgram(response.data.data);
            })
            .catch(error => {
                console.log(error);
            });

    }, []);

    const isValidIndex = activeTab >= 0;
    const currentProgram = porgrams.find(program => program.id === activeTab);


    const sessionsProgram3 = currentProgram?.sessionsProgram.map((session) => (
        <div>


            <div
                className="w-full text-center md:m-5 md:ml-[54] my-6 flex md:flex-row flex-col align-items-center  "><span
                className="text-lg">{session.start_time} - {session.end_time}</span> <span
                className="ml-5 text-lg font-bold">{session.name}</span></div>

            <SpeakerList session={session}/>
        </div>


    ));

    return (
        <div className="mb-[100]">
            <h1 className="text-2xl text-center mt-12 uppercase">Program</h1>
            <TapsSessions programsArray={porgrams} activeTab={activeTab} setActiveTab={setActiveTab}/>

            {currentProgram ? (
                <div className="w-full text-center">{sessionsProgram3}</div>
            ) : (
                <p>No program selected or invalid tab index</p>
            )}
        </div>
    );
}
