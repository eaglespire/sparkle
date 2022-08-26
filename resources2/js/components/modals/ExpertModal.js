
import React from 'react'
import '/modals/modal.css'

export default function ExpertModal({title}){
    return (
        <>
            <div className="modalBackground">
                <div className="modalContainer">
                    <div className="row justify-content-center">
                        <div className="col-7">
                            <div className="card">
                                <div className="card-header">
                                    <h6 className="card-title">I am the modal title</h6>
                                </div>
                                <div className="card-body">
                                    <p>I am the card body...</p>
                                </div>
                                <div className="card-footer">
                                    <p>I am the card footer</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
