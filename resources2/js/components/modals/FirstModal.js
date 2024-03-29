
import React from 'react'

export default function FirstModal({launchSecondModal}){
    return (
        <>
            <div className="modal fade" id="firstModal" tabIndex="-1">
                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title"
                                id="exampleModalLabel">Modal title</h5>
                            <button type="button" className="btn-close"
                                    data-bs-dismiss="modal"></button>
                        </div>
                        <div className="modal-body">
                            <p>This is modal one</p>
                        </div>
                        <div className="modal-footer">
                            <button type="button" className="btn btn-secondary"
                                    data-bs-dismiss="modal">Close
                            </button>
                            <button onClick={()=>launchSecondModal()} type="button"
                                    className="btn btn-primary">Save changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
