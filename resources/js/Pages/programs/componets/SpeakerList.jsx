import React from 'react';


import {Avatar, Card} from "flowbite-react";
export function SpeakerList({ session }) {


    return (

        <Card className="w-full max-w-sm p-0 bg-transparent  border-transparent md:ml-[65] align-items-center justify-center" >

            <div className=" text-center flex   flex-col  gap-8  justify-center md:justify-start">
                {session.speakers.map((speaker) => (
                    <div key={speaker.id} className="flex  mx-3 mb-3">
                        <Avatar
                            className="justify-center "
                            size="lg"
                            img={`api/images/${speaker.image}`}
                            rounded
                            alt={speaker.name_en}
                        />
                        <div className="md:ml-3">
                            <span className="text-medium block mt-4">{speaker.name_en}</span>
                            <span className="text-medium block mt-2">{speaker.job_en}</span>
                            {/* Uncomment if you want to display speaker title */}
                            {/* <span className="text-2xl">{speaker.title}</span> */}
                        </div>
                    </div>
                ))}
            </div>

        </Card>


    );
}
