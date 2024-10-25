import React, {useEffect} from 'react';

// Import your global CSS file
import "../global.css"

import { useFonts } from "expo-font";
import {SplashScreen, Stack} from "expo-router";
import GlobalProvider from "../context/GlobalProvider";
import {verifyInstallation} from "nativewind";

// Prevent the splash screen from auto-hiding before asset loading is complete.
SplashScreen.preventAutoHideAsync();

const RootLayout = () => {

    // verifyInstallation();

    const [fontsLoaded, error] = useFonts({
        "Archivo-Black": require("../assets/fonts/Archivo-Black.ttf"),
        "Archivo-Bold": require("../assets/fonts/Archivo-Bold.ttf"),
        "Archivo-ExtraBold": require("../assets/fonts/Archivo-ExtraBold.ttf"),
        "Archivo-ExtraLight": require("../assets/fonts/Archivo-ExtraLight.ttf"),
        "Archivo-Light": require("../assets/fonts/Archivo-Light.ttf"),
        "Archivo-Medium": require("../assets/fonts/Archivo-Medium.ttf"),
        "Archivo-Regular": require("../assets/fonts/Archivo-Regular.ttf"),
        "Archivo-SemiBold": require("../assets/fonts/Archivo-SemiBold.ttf"),
        "Archivo-Thin": require("../assets/fonts/Archivo-Thin.ttf"),
    });

    useEffect(() => {
        if (error) throw error;

        if (fontsLoaded) {
            SplashScreen.hideAsync();
        }
    }, [fontsLoaded, error]);

    if (!fontsLoaded) {
        return null;
    }

    if (!fontsLoaded && !error) {
        return null;
    }

    return (
        <GlobalProvider>
            <Stack>
                <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
                <Stack.Screen name="(auth)" options={{ headerShown: false }} />
                <Stack.Screen name="index" options={{ headerShown: false }} />
            </Stack>
        </GlobalProvider>
    );
};

export default RootLayout;