// import * as postVotes from './postVotes';


// Map of button classes to their respective handlers
const buttonHandlers = {
    // 'post-upvote-button': (event, target) => postVotes.handleVote(event, target, true),
};



function handleButtonClick(event) {
    let target = event.target;

    while (target && target !== document.body) {
        const handlerEntry = Object.entries(buttonHandlers).find(([className]) =>
            target.classList.contains(className)
        );

        if (handlerEntry) {
            const [className, handler] = handlerEntry;
            handler(event, target); // Pass the event and target to the handler function
            return; // Break after finding the handler
        }

        target = target.parentElement;
    }
}


export function handleButtonClicks() {
    document.body.addEventListener('click', handleButtonClick);
}